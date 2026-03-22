<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\User;
use App\Notifications\NewAppointmentCreated;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\AES;

class WhatsAppFlowController extends Controller
{
    private $privateKey;

    public function __construct()
    {
        $this->privateKey = file_get_contents(storage_path('app/whatsapp_private.pem'));
        if (!$this->privateKey) {
            throw new \Exception('Private key not found');
        }
    }

    public function handle(Request $request)
    {
        $body = $request->all();

        $decrypted = $this->decryptPayload($body);
        if (!$decrypted) {
            return response('', 421);
        }

        $payload = json_decode($decrypted['decryptedBody'], true);

        if (isset($payload['action']) && $payload['action'] === 'ping') {
            $response = ['data' => ['status' => 'active']];
            $encryptedResponse = $this->encryptResponse($response, $decrypted['aesKey'], $decrypted['iv']);
            return response($encryptedResponse, 200)->header('Content-Type', 'text/plain');
        }

        $action = $payload['action'] ?? 'INIT';
        $screen = $payload['screen'] ?? 'TYPE';
        $data = $payload['data'] ?? [];
        $flowToken = $payload['flow_token'] ?? null;

        $response = $this->processFlow($action, $screen, $data, $flowToken);

        Log::info('WhatsApp Flow response (plain):', $response);

        $encryptedResponse = $this->encryptResponse($response, $decrypted['aesKey'], $decrypted['iv']);

        return response($encryptedResponse, 200)->header('Content-Type', 'text/plain');
    }

    private function decryptPayload(array $body): ?array
    {
        try {
            $encryptedAesKey = base64_decode($body['encrypted_aes_key'] ?? '');
            $encryptedData = base64_decode($body['encrypted_flow_data'] ?? '');
            $iv = base64_decode($body['initial_vector'] ?? '');

            $rsa = RSA::load($this->privateKey)
                ->withPadding(RSA::ENCRYPTION_OAEP)
                ->withHash('sha256')
                ->withMGFHash('sha256');

            $aesKey = $rsa->decrypt($encryptedAesKey);
            if (!$aesKey)
                throw new \Exception('AES key decryption failed');

            $aes = new AES('gcm');
            $aes->setKey($aesKey);
            $aes->setNonce($iv);

            $tagLength = 16;
            $ciphertext = substr($encryptedData, 0, -$tagLength);
            $tag = substr($encryptedData, -$tagLength);

            $aes->setTag($tag);
            $decrypted = $aes->decrypt($ciphertext);

            if (!$decrypted)
                throw new \Exception('Flow data decryption failed');

            return [
                'decryptedBody' => $decrypted,
                'aesKey' => $aesKey,
                'iv' => $iv,
            ];
        } catch (\Exception $e) {
            Log::error('Flow decryption error: ' . $e->getMessage());
            return null;
        }
    }

    private function encryptResponse(array $responseData, string $aesKey, string $iv): string
    {
        $json = json_encode($responseData);

        $flippedIv = '';
        for ($i = 0; $i < strlen($iv); $i++) {
            $flippedIv .= chr(~ord($iv[$i]) & 0xFF);
        }

        $aes = new AES('gcm');
        $aes->setKey($aesKey);
        $aes->setNonce($flippedIv);

        $encrypted = $aes->encrypt($json);
        $tag = $aes->getTag();

        return base64_encode($encrypted . $tag);
    }

    private function normalizeId($value): ?int
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }
        $id = filter_var($value, FILTER_VALIDATE_INT);
        return $id !== false && $id > 0 ? $id : null;
    }

    private function processFlow(string $action, string $screen, array $data, ?string $flowToken): array
    {
        Log::info('Data):', $data);

        $response = [
            'version' => '3.0',
            'screen' => $screen,
            'data' => [
                'error_message' => '',
            ],
        ];

        if ($action === 'INIT') {
            $response['screen'] = 'TYPE';
            return $response;
        }

        if ($screen === 'TYPE' && $action === 'data_exchange') {
            $type = $data['appointment_type'] ?? null;

            if (!$type || !in_array($type, ['specific', 'any'])) {
                $response['data']['error_message'] = 'Please select a valid appointment type';
                return $response;
            }

            $nextScreen = ($type === 'specific') ? 'DOCTOR_SELECT' : 'SPECIALIZATION';

            $response['screen'] = $nextScreen;
            $response['data']['appointment_type'] = $type;

            if ($nextScreen === 'DOCTOR_SELECT') {
                $response['data']['doctors'] = Doctor::active()
                    ->orderBy('first_name')
                    ->orderBy('last_name')
                    ->get()
                    ->map(fn($d) => ['id' => (string) $d->id, 'title' => $d->getFullNameAttribute()])
                    ->toArray();
            } else {
                $response['data']['specializations'] = Specialization::orderBy('name')
                    ->get()
                    ->map(fn($s) => ['id' => (string) $s->id, 'title' => $s->name])
                    ->toArray();
            }

            return $response;
        }

        if ($screen === 'SPECIALIZATION' && $action === 'data_exchange') {
            if (($data['trigger'] ?? '') === 'specialization_selected') {
                $specId = $this->normalizeId($data['specialization'] ?? null);

                if ($specId === null) {
                    $response['data']['error_message'] = 'Invalid specialization';
                    return $response;
                }

                $doctors = Doctor::where('primary_specialization_id', $specId)
                    ->active()
                    ->orderBy('first_name')
                    ->orderBy('last_name')
                    ->get()
                    ->map(fn($d) => ['id' => (string) $d->id, 'title' => $d->getFullNameAttribute()])
                    ->toArray();

                $response['data']['doctors'] = $doctors;
                $response['data']['specialization'] = (string) $specId;
                $response['data']['error_message'] = empty($doctors) ? 'No doctors available for this specialization' : '';
                return $response;
            }

            $response['screen'] = 'VISIT_TYPE';
            $response['data']['appointment_type'] = $data['appointment_type'] ?? 'any';
            $response['data']['specialization'] = $data['specialization'] ? (string) $this->normalizeId($data['specialization']) : '';
            return $response;
        }

        if ($screen === 'DOCTOR_SELECT' && $action === 'data_exchange') {
            $doctorId = $this->normalizeId($data['doctor'] ?? null);

            if ($doctorId === null || !Doctor::where('id', $doctorId)->active()->exists()) {
                $response['data']['error_message'] = 'Please select a valid doctor';
                return $response;
            }

            $specializationId = Doctor::where('id', $doctorId)
                ->active()
                ->value('primary_specialization_id');

            $response['screen'] = 'VISIT_TYPE';
            $response['data']['appointment_type'] = $data['appointment_type'] ?? 'specific';
            $response['data']['doctor'] = (string) ($doctorId ?? '');
            $response['data']['specialization'] = $specializationId !== null ? (string) $specializationId : '';

            return $response;
        }

        if ($screen === 'VISIT_TYPE' && $action === 'data_exchange') {
            $visitType = $data['visit_type'] ?? null;

            if (!in_array($visitType, ['first', 'followup'])) {
                $response['data']['error_message'] = 'Please select visit type';
                return $response;
            }

            $nextScreen = ($visitType === 'first') ? 'PATIENT_NEW' : 'MRN_ONLY';

            $response['screen'] = $nextScreen;
            $response['data']['appointment_type'] = $data['appointment_type'] ?? 'specific';
            $response['data']['visit_type'] = $visitType;
            $response['data']['doctor'] = $data['doctor'] ?? '';
            $response['data']['specialization'] = $data['specialization'] ?? '';
            return $response;
        }

        if ($screen === 'PATIENT_NEW' && $action === 'data_exchange' && ($data['trigger'] ?? '') === 'create_patient') {
            $validator = \Validator::make($data, [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required|string|regex:/^\+?[0-9]{9,15}$/|unique:patients,phone',
                'email' => 'nullable|email|unique:patients,email',
                'dob' => 'required|date',
                'gender' => 'required|in:male,female,other',
                'address' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                $response['data']['error_message'] = implode(', ', $validator->errors()->all());
                return $response;
            }

            $lastPatient = Patient::orderBy('id', 'desc')->first();
            $nextNumber = $lastPatient ? $lastPatient->id + 1 : 1;
            $mrn = 'MRN-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $patient = Patient::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'date_of_birth' => $data['dob'],
                'gender' => $data['gender'],
                'address' => $data['address'] ?? null,
                'medical_record_number' => $mrn,
                'is_active' => true,
                'is_deleted' => false,
            ]);

            $response['screen'] = 'PREFERRED_TIME';
            $response['data']['patient'] = (string) $patient->id;
            $response['data']['appointment_type'] = $data['appointment_type'] ?? 'specific';
            $response['data']['visit_type'] = 'first';
            $response['data']['doctor'] = $data['doctor'] ?? '';
            $response['data']['specialization'] = $data['specialization'] ?? '';
            return $response;
        }

        if ($screen === 'MRN_ONLY' && $action === 'data_exchange' && ($data['trigger'] ?? '') === 'lookup_patient_by_mrn') {
            $mrn = trim($data['mrn'] ?? '');

            if (empty($mrn)) {
                $response['data']['error_message'] = 'Please enter Medical Record Number';
                return $response;
            }

            $patient = Patient::where('medical_record_number', $mrn)
                ->where('is_active', true)
                ->where('is_deleted', false)
                ->first();

            if (!$patient) {
                $response['data']['error_message'] = 'No patient found with this MRN';
                return $response;
            }

            $response['screen'] = 'PREFERRED_TIME';
            $response['data']['patient'] = (string) $patient->id;
            $response['data']['appointment_type'] = $data['appointment_type'] ?? 'specific';
            $response['data']['visit_type'] = 'followup';
            $response['data']['doctor'] = $data['doctor'] ?? '';
            $response['data']['specialization'] = $data['specialization'] ?? '';
            return $response;
        }

        if ($screen === 'PREFERRED_TIME' && $action === 'data_exchange') {
            $pref = $data['preferred_time'] ?? null;

            if (!in_array($pref, ['next', '7days', '15days'])) {
                $response['data']['error_message'] = 'Please select preferred time option';
                return $response;
            }

            $response['screen'] = 'REASON';
            $response['data']['patient'] = $data['patient'] ?? '';
            $response['data']['appointment_type'] = $data['appointment_type'] ?? 'specific';
            $response['data']['visit_type'] = $data['visit_type'] ?? 'first';
            $response['data']['preferred_time'] = $pref;
            $response['data']['doctor'] = $data['doctor'] ?? '';
            $response['data']['specialization'] = $data['specialization'] ?? '';
            return $response;
        }

        if ($screen === 'REASON' && $action === 'data_exchange') {
            $validator = \Validator::make($data, [
                'reason' => 'required|string|max:1000',
                'notes' => 'nullable|string|max:2000',
            ]);

            if ($validator->fails()) {
                $response['data']['error_message'] = implode(', ', $validator->errors()->all());
                return $response;
            }

            $response['screen'] = 'SUMMARY';
            $response['data']['patient'] = $data['patient'] ?? '';
            $response['data']['doctor'] = $data['doctor'] ?? '';
            $response['data']['specialization'] = $data['specialization'] ?? '';
            $response['data']['appointment_type'] = $data['appointment_type'] ?? 'specific';
            $response['data']['visit_type'] = $data['visit_type'] ?? 'first';
            $response['data']['preferred_time'] = $data['preferred_time'] ?? 'next';
            $response['data']['reason'] = $data['reason'] ?? '';
            $response['data']['notes'] = $data['notes'] ?? '';
            $response['data']['details_text'] = "Patient ID: {$data['patient']}\nType: {$data['appointment_type']}\nVisit: {$data['visit_type']}\nTime preference: {$data['preferred_time']}\nReason: {$data['reason']}";
            return $response;
        }

        if ($screen === 'SUMMARY' && $action === 'data_exchange' && !empty($data['complete'])) {
            $doctorId = $this->normalizeId($data['doctor'] ?? '');
            $specializationId = $this->normalizeId($data['specialization'] ?? '');

            $validator = \Validator::make($data, [
                'patient' => 'required|exists:patients,id',
                'appointment_type' => ['required', Rule::in(['specific', 'any'])],
                'reason' => 'required|string|max:1000',
                'notes' => 'nullable|string|max:2000',
            ]);

            if ($data['appointment_type'] === 'specific') {
                if ($doctorId === null) {
                    $response['data']['error_message'] = 'Doctor is required for specific appointments';
                    return $response;
                }
                $validator->after(function ($validator) use ($doctorId) {
                    if (!Doctor::where('id', $doctorId)->active()->exists()) {
                        $validator->errors()->add('doctor', 'Selected doctor is not valid or not active');
                    }
                });
            } else {
                if ($specializationId === null) {
                    $response['data']['error_message'] = 'Specialization is required when choosing any doctor';
                    return $response;
                }
                $validator->after(function ($validator) use ($specializationId) {
                    if (!Specialization::where('id', $specializationId)->exists()) {
                        $validator->errors()->add('specialization', 'Selected specialization is not valid');
                    }
                });
            }

            if ($validator->fails()) {
                $response['data']['error_message'] = implode(', ', $validator->errors()->all());
                return $response;
            }

            $appointment = Appointment::create([
                'patient_id' => $data['patient'],
                'appointment_type' => $data['appointment_type'],
                'reason_for_visit' => $data['reason'],
                'patient_notes' => $data['notes'] ?? null,
                'status' => Appointment::STATUS_PENDING,
                'doctor_id' => $doctorId,
                'specialization_id' => $specializationId,
            ]);

            $recipients = User::role(['admin', 'receptionist'])->get();
            Notification::send($recipients, new NewAppointmentCreated($appointment));

            $response['data']['success_message'] = 'Appointment request submitted successfully!';
            $response['data']['appointment_id'] = (string) $appointment->id;
            $response['data']['error_message'] = '';
            return $response;
        }

        $response['data']['error_message'] = 'Invalid action or screen';
        return $response;
    }
}
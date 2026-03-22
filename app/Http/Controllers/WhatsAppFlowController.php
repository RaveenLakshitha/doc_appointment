<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\User;
use App\Notifications\NewAppointmentCreated;
use App\Models\Specialization;
use App\Models\AgeGroup;
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
        Log::info('Processing screen: ' . $screen, $data);

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
                $response['data']['age_groups'] = $this->getAgeGroupsArray();
                $response['data']['error_message'] = empty($doctors) ? 'No doctors available for this specialization' : '';

                return $response;
            }

            $response['screen'] = 'AGE_GROUP';
            $response['data']['appointment_type'] = $data['appointment_type'] ?? 'any';
            $response['data']['specialization'] = $data['specialization'] ? (string) $this->normalizeId($data['specialization']) : '';
            $response['data']['age_groups'] = $this->getAgeGroupsArray();

            return $response;
        }

        if ($screen === 'DOCTOR_SELECT' && $action === 'data_exchange') {
            $doctorId = $this->normalizeId($data['doctor'] ?? null);

            if ($doctorId === null || !Doctor::where('id', $doctorId)->active()->exists()) {
                $response['data']['error_message'] = 'Please select a valid doctor';
                return $response;
            }

            $doctor = Doctor::find($doctorId);
            $specializationId = $doctor ? $doctor->primary_specialization_id : null;

            $response['screen'] = 'AGE_GROUP';
            $response['data']['appointment_type'] = $data['appointment_type'] ?? 'specific';
            $response['data']['doctor'] = (string) $doctorId;
            $response['data']['specialization'] = $specializationId ? (string) $specializationId : '';
            $response['data']['age_groups'] = $this->getAgeGroupsArray();
            $response['data']['age_group'] = '';
            $response['data']['preferred_language'] = $data['preferred_language'] ?? '';
            $response['data']['visit_type'] = '';

            return $response;
        }

        if ($screen === 'AGE_GROUP' && $action === 'data_exchange') {
            $ageGroupId = $this->normalizeId($data['age_group'] ?? null);

            if ($ageGroupId === null || !AgeGroup::where('id', $ageGroupId)->where('is_active', true)->exists()) {
                $response['data']['error_message'] = 'Please select a valid age group';
                return $response;
            }

            $appointmentType = $data['appointment_type'] ?? 'any';
            $nextScreen = 'PREFERRED_LANGUAGE';

            $response['screen'] = $nextScreen;
            $response['data']['appointment_type'] = $appointmentType;
            $response['data']['doctor'] = $data['doctor'] ?? '';
            $response['data']['specialization'] = $data['specialization'] ?? '';
            $response['data']['age_group'] = (string) $ageGroupId;
            $response['data']['preferred_language'] = $data['preferred_language'] ?? '';
            $response['data']['visit_type'] = '';

            return $response;
        }

        if ($screen === 'PREFERRED_LANGUAGE' && $action === 'data_exchange') {
            $lang = $data['preferred_language'] ?? null;

            if (!in_array($lang, ['en', 'es'])) {
                $response['data']['error_message'] = 'Please select a valid language';
                return $response;
            }

            $response['screen'] = 'VISIT_TYPE';
            $response['data']['appointment_type'] = $data['appointment_type'] ?? 'any';
            $response['data']['specialization'] = $data['specialization'] ?? '';
            $response['data']['age_group'] = $data['age_group'] ?? '';
            $response['data']['preferred_language'] = $lang;
            $response['data']['doctor'] = $data['doctor'] ?? '';

            return $response;
        }

        if ($screen === 'VISIT_TYPE' && $action === 'data_exchange') {
            $visitType = $data['visit_type'] ?? null;

            if (!in_array($visitType, ['first', 'followup'])) {
                $response['data']['error_message'] = 'Please select visit type';
                return $response;
            }

            $nextScreen = ($visitType === 'first') ? 'PATIENT_NEW' : 'PHONE_LOOKUP';

            $response['screen'] = $nextScreen;
            $response['data']['appointment_type'] = $data['appointment_type'] ?? 'specific';
            $response['data']['visit_type'] = $visitType;
            $response['data']['doctor'] = $data['doctor'] ?? '';
            $response['data']['specialization'] = $data['specialization'] ?? '';
            $response['data']['age_group'] = $data['age_group'] ?? '';
            $response['data']['preferred_language'] = $data['preferred_language'] ?? 'en';

            return $response;
        }

        if ($screen === 'PHONE_LOOKUP' && $action === 'data_exchange' && ($data['trigger'] ?? '') === 'lookup_patient_by_phone') {
            $phone = trim($data['phone'] ?? '');

            if (empty($phone)) {
                $response['data']['error_message'] = 'Please enter your phone number';
                return $response;
            }

            $phone = preg_replace('/[^0-9+]/', '', $phone);

            $patients = Patient::where('phone', $phone)
                ->where('is_active', true)
                ->where('is_deleted', false)
                ->orderBy('first_name')
                ->get();

            if ($patients->isEmpty()) {
                $response['data']['error_message'] = 'No patient found with this phone number';
                return $response;
            }

            if ($patients->count() === 1) {
                $patient = $patients->first();
                $response['screen'] = 'PREFERRED_TIME';
                $response['data']['patient'] = (string) $patient->id;
                $response['data']['patient_details'] = "Patient: {$patient->first_name} {$patient->last_name}\nPhone: {$patient->phone}\nGender: " . ucfirst($patient->gender);
                $response['data']['appointment_type'] = $data['appointment_type'] ?? 'specific';
                $response['data']['visit_type'] = 'followup';
                $response['data']['doctor'] = $data['doctor'] ?? '';
                $response['data']['specialization'] = $data['specialization'] ?? '';
                $response['data']['age_group'] = $data['age_group'] ?? '';
                $response['data']['preferred_language'] = $data['preferred_language'] ?? 'en';
                return $response;
            }

            $patientList = $patients->map(function ($p) {
                $age = $p->date_of_birth ? Carbon::parse($p->date_of_birth)->age : null;
                $display = trim("{$p->first_name} {$p->last_name}");
                if ($age !== null)
                    $display .= " ({$age} yrs)";
                if ($p->gender)
                    $display .= " • " . ucfirst($p->gender);
                return [
                    'id' => (string) $p->id,
                    'title' => $display,
                ];
            })->toArray();

            $response['screen'] = 'PATIENT_SELECT';
            $response['data']['patients'] = $patientList;
            $response['data']['phone'] = $phone;
            $response['data']['appointment_type'] = $data['appointment_type'] ?? 'specific';
            $response['data']['visit_type'] = 'followup';
            $response['data']['doctor'] = $data['doctor'] ?? '';
            $response['data']['specialization'] = $data['specialization'] ?? '';
            $response['data']['age_group'] = $data['age_group'] ?? '';
            $response['data']['preferred_language'] = $data['preferred_language'] ?? 'en';

            return $response;
        }

        if ($screen === 'PATIENT_SELECT' && $action === 'data_exchange' && ($data['trigger'] ?? '') === 'select_patient') {
            $selectedId = $this->normalizeId($data['selected_patient'] ?? null);

            if ($selectedId === null) {
                $response['data']['error_message'] = 'Please select a patient';
                return $response;
            }

            $patient = Patient::where('id', $selectedId)
                ->where('is_active', true)
                ->where('is_deleted', false)
                ->first();

            if (!$patient) {
                $response['data']['error_message'] = 'Selected patient not found or inactive';
                return $response;
            }

            $response['screen'] = 'PREFERRED_TIME';
            $response['data']['patient'] = (string) $patient->id;
            $response['data']['patient_details'] = "Patient: {$patient->first_name} {$patient->last_name}\nPhone: {$patient->phone}\nGender: " . ucfirst($patient->gender);
            $response['data']['appointment_type'] = $data['appointment_type'] ?? 'specific';
            $response['data']['visit_type'] = 'followup';
            $response['data']['doctor'] = $data['doctor'] ?? '';
            $response['data']['specialization'] = $data['specialization'] ?? '';
            $response['data']['age_group'] = $data['age_group'] ?? '';
            $response['data']['preferred_language'] = $data['preferred_language'] ?? 'en';

            return $response;
        }

        if ($screen === 'PATIENT_NEW' && $action === 'data_exchange' && ($data['trigger'] ?? '') === 'create_patient') {
            $validator = \Validator::make($data, [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required|string|regex:/^\+?[0-9]{9,15}$/',
                'email' => 'nullable|email',
                'age' => 'required|integer|min:0|max:120',
                'gender' => 'required|in:male,female,other',
                'address' => 'nullable|string|max:500',
                'previous_psychotherapy' => 'nullable|in:yes,no',
                'preferred_session_time' => 'nullable|string|max:255',
                'referred_by' => 'nullable|string|max:255',
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
                'age' => (int) $data['age'],
                'gender' => $data['gender'],
                'address' => $data['address'] ?? null,
                'attended_psychotherapy' => ($data['previous_psychotherapy'] ?? '') === 'yes',
                'preferred_session_time' => $data['preferred_session_time'] ?? null,
                'recommended_by' => $data['referred_by'] ?? null,
                'medical_record_number' => $mrn,
                'is_active' => true,
                'is_deleted' => false,
            ]);

            $response['screen'] = 'PREFERRED_TIME';
            $response['data']['patient'] = (string) $patient->id;
            $response['data']['patient_details'] = "Patient: {$patient->first_name} {$patient->last_name}\nPhone: {$patient->phone}\nGender: " . ucfirst($patient->gender);
            $response['data']['appointment_type'] = $data['appointment_type'] ?? 'specific';
            $response['data']['visit_type'] = 'first';
            $response['data']['doctor'] = $data['doctor'] ?? '';
            $response['data']['specialization'] = $data['specialization'] ?? '';
            $response['data']['age_group'] = $data['age_group'] ?? '';
            $response['data']['preferred_language'] = $data['preferred_language'] ?? 'en';

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
            $response['data']['age_group'] = $data['age_group'] ?? '';
            $response['data']['preferred_language'] = $data['preferred_language'] ?? 'en';

            return $response;
        }

        if ($screen === 'REASON' && $action === 'data_exchange' && ($data['trigger'] ?? '') === 'review_appointment') {
            $validator = \Validator::make($data, [
                'reason' => 'required|string|max:1000',
                'notes' => 'nullable|string|max:2000',
            ]);

            if ($validator->fails()) {
                $response['data']['error_message'] = implode(', ', $validator->errors()->all());
                return $response;
            }

            $summary = [];

            $patient = \App\Models\Patient::find($this->normalizeId($data['patient'] ?? null));
            if ($patient) {
                $summary[] = "👤 Patient: " . $patient->first_name . " " . $patient->last_name;
                $summary[] = "📱 Phone: " . $patient->phone;
            }

            if (($data['appointment_type'] ?? '') === 'specific' && !empty($data['doctor'])) {
                $doc = \App\Models\Doctor::find($this->normalizeId($data['doctor']));
                if ($doc)
                    $summary[] = "🧑‍⚕️ Doctor: " . $doc->full_name;
            } elseif (($data['appointment_type'] ?? '') === 'any' && !empty($data['specialization'])) {
                $spec = \App\Models\Specialization::find($this->normalizeId($data['specialization']));
                if ($spec)
                    $summary[] = "🏥 Department: " . $spec->name;
            }

            $prefTimeMap = [
                'next' => 'Next available slot',
                '7days' => 'Within next 7 days',
                '15days' => 'Within next 15 days',
            ];
            $prefTime = $prefTimeMap[$data['preferred_time'] ?? ''] ?? 'Next available slot';
            $summary[] = "🕒 Preferred Time: " . $prefTime;

            $summary[] = "📝 Reason: " . $data['reason'];

            if (!empty($data['notes'])) {
                $summary[] = "📌 Notes: " . $data['notes'];
            }

            $response['screen'] = 'APPOINTMENT_SUMMARY';

            $response['data'] = $data;
            $response['data']['summary_text'] = implode("\n", $summary);
            $response['data']['error_message'] = '';

            return $response;
        }

        if ($screen === 'APPOINTMENT_SUMMARY' && $action === 'data_exchange' && !empty($data['complete'])) {
            $validator = \Validator::make($data, [
                'reason' => 'required|string|max:1000',
                'notes' => 'nullable|string|max:2000',
                'patient' => 'required|exists:patients,id',
            ]);

            if ($validator->fails()) {
                $response['data']['error_message'] = implode(', ', $validator->errors()->all());
                return $response;
            }

            $doctorId = $this->normalizeId($data['doctor'] ?? '');
            $specializationId = $this->normalizeId($data['specialization'] ?? '');
            $ageGroupId = $this->normalizeId($data['age_group'] ?? '');

            if ($data['appointment_type'] === 'specific' && $doctorId === null) {
                $response['data']['error_message'] = 'Doctor is required';
                return $response;
            }
            if ($data['appointment_type'] === 'any' && $specializationId === null) {
                $response['data']['error_message'] = 'Specialization is required';
                return $response;
            }

            $langSlug = $data['preferred_language'] ?? 'en';
            $langOption = \App\Models\OptionList::where('type', 'language')->where('slug', $langSlug)->first();

            $appointment = Appointment::create([
                'patient_id' => $data['patient'],
                'appointment_type' => $data['appointment_type'],
                'reason_for_visit' => $data['reason'],
                'patient_notes' => $data['notes'] ?? null,
                'status' => Appointment::STATUS_PENDING,
                'doctor_id' => $doctorId,
                'specialization_id' => $specializationId,
                'age_group_id' => $ageGroupId,
                'preferred_language_id' => $langOption ? $langOption->id : null,
                'preferred_time' => $data['preferred_time'] ?? null,
            ]);

            $recipients = User::role(['admin', 'receptionist'])->get();
            Notification::send($recipients, new NewAppointmentCreated($appointment));

            $response['screen'] = 'SUCCESS';
            $response['data'] = [
                'appointment_id' => (string) $appointment->id,
                'success_message' => 'Your appointment request has been submitted successfully! Our team will contact you soon.',
                'details_text' => "Reason: " . ($data['reason'] ?? 'Not specified')
                    . "\nPreferred Time: " . ucfirst($data['preferred_time'] ?? 'Next available')
                    . "\nPatient ID: " . $data['patient']
                    . ($data['notes'] ? "\nNotes: " . $data['notes'] : ''),
                'error_message' => '',
            ];

            return $response;
        }

        $response['data']['error_message'] = 'Invalid action or screen';
        return $response;
    }

    private function getAgeGroupsArray(): array
    {
        return AgeGroup::where('is_active', true)
            ->orderBy('min_age')
            ->get()
            ->map(function ($ag) {
                $title = $ag->name;
                if ($ag->description) {
                    $title .= " ({$ag->description})";
                }
                return [
                    'id' => (string) $ag->id,
                    'title' => $title,
                ];
            })
            ->toArray();
    }
}
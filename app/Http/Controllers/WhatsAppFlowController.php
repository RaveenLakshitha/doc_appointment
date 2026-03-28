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
        Log::info('Processing (wrapper) screen: ' . $screen, $data);

        if ($action === 'INIT') {
            return [
                'version' => '3.0',
                'screen' => 'LANGUAGE_SELECT',
                'data' => ['error_message' => '']
            ];
        }

        if ($screen === 'LANGUAGE_SELECT' && $action === 'data_exchange') {
            $lang = $data['preferred_language'] ?? 'en';
            $suffix = ($lang === 'es') ? '_ES' : '_EN';
            return [
                'version' => '3.0',
                'screen' => 'TYPE' . $suffix,
                'data' => [
                    'appointment_type' => '',
                    'preferred_language' => $lang,
                    'error_message' => ''
                ]
            ];
        }

        $suffix = '';
        if (preg_match('/_(EN|ES)$/', $screen, $matches)) {
            $suffix = '_' . $matches[1];
            $screen = preg_replace('/_(EN|ES)$/', '', $screen);
        }

        $response = $this->processFlowInternal($action, $screen, $data, $flowToken);

        if (isset($response['screen']) && !preg_match('/_(EN|ES)$/', $response['screen'])) {
            $response['screen'] .= $suffix;
        }

        if (isset($data['preferred_language'])) {
            $response['data']['preferred_language'] = $data['preferred_language'];
        }

        return $response;
    }

    private function processFlowInternal(string $action, string $screen, array $data, ?string $flowToken): array
    {
        Log::info('Processing internal screen: ' . $screen, $data);

        $response = [
            'version' => '3.0',
            'screen' => $screen,
            'data' => [
                'error_message' => '',
            ],
        ];

        if ($screen === 'TYPE' && $action === 'data_exchange') {
            $type = $data['appointment_type'] ?? null;

            if (!$type || !in_array($type, ['specific', 'any'])) {
                $response['data']['error_message'] = ($data['preferred_language'] ?? 'en') === 'es' ? 'Por favor seleccione un tipo de cita válido' : 'Please select a valid appointment type';
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
                $activePrimaryIds = Doctor::active()->pluck('primary_specialization_id')->filter()->unique();
                $response['data']['specializations'] = Specialization::where(function ($query) use ($activePrimaryIds) {
                    $query->whereHas('doctors', function ($q) {
                        $q->active();
                    })->orWhereIn('id', $activePrimaryIds);
                })
                    ->orderBy('name')
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
                    $response['data']['error_message'] = ($data['preferred_language'] ?? 'en') === 'es' ? 'Especialidad inválida' : 'Invalid specialization';
                    return $response;
                }

                $doctors = Doctor::active()
                    ->where(function ($query) use ($specId) {
                        $query->where('primary_specialization_id', $specId)
                            ->orWhereHas('specializations', function ($q) use ($specId) {
                                $q->where('specializations.id', $specId);
                            });
                    })
                    ->orderBy('first_name')
                    ->orderBy('last_name')
                    ->get()
                    ->map(fn($d) => ['id' => (string) $d->id, 'title' => $d->getFullNameAttribute()])
                    ->toArray();

                $response['data']['doctors'] = $doctors;
                $response['data']['specialization'] = (string) $specId;
                $response['data']['age_groups'] = $this->getAgeGroupsArray();
                $response['data']['error_message'] = empty($doctors) ? (($data['preferred_language'] ?? 'en') === 'es' ? 'No hay terapeutas disponibles para esta especialidad' : 'No therapists available for this specialization') : '';

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
                $response['data']['error_message'] = ($data['preferred_language'] ?? 'en') === 'es' ? 'Por favor seleccione tipo de visita' : 'Please select visit type';
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
                $response['data']['error_message'] = ($data['preferred_language'] ?? 'en') === 'es' ? 'Por favor ingrese su número de teléfono' : 'Please enter your phone number';
                return $response;
            }

            $phone = preg_replace('/[^0-9+]/', '', $phone);

            $patients = Patient::where('phone', $phone)
                ->where('is_active', true)
                ->where('is_deleted', false)
                ->orderBy('first_name')
                ->get();

            if ($patients->isEmpty()) {
                $response['data']['error_message'] = ($data['preferred_language'] ?? 'en') === 'es' ? 'No se encontró ningún paciente con este número de teléfono' : 'No patient found with this phone number';
                return $response;
            }

            if ($patients->count() === 1) {
                $patient = $patients->first();
                $response['screen'] = 'PREFERRED_TIME';
                $response['data']['patient'] = (string) $patient->id;
                $is_es = ($data['preferred_language'] ?? 'en') === 'es';
                $genderStr = ucfirst($patient->gender);
                if ($is_es) {
                    $genderStr = match (strtolower($patient->gender)) {
                        'male' => 'Masculino',
                        'female' => 'Femenino',
                        default => ucfirst($patient->gender)
                    };
                }
                $patientLabel = $is_es ? "Paciente" : "Patient";
                $phoneLabel = $is_es ? "Teléfono" : "Phone";
                $genderLabel = $is_es ? "Género" : "Gender";

                $response['data']['patient_details'] = "{$patientLabel}: {$patient->first_name} {$patient->last_name}\n{$phoneLabel}: {$patient->phone}\n{$genderLabel}: " . $genderStr;
                $response['data']['appointment_type'] = $data['appointment_type'] ?? 'specific';
                $response['data']['visit_type'] = 'followup';
                $response['data']['doctor'] = $data['doctor'] ?? '';
                $response['data']['specialization'] = $data['specialization'] ?? '';
                $response['data']['age_group'] = $data['age_group'] ?? '';
                $response['data']['preferred_language'] = $data['preferred_language'] ?? 'en';
                return $response;
            }

            $patientList = $patients->map(function ($p) use ($data) {
                $is_es = ($data['preferred_language'] ?? 'en') === 'es';
                $age = $p->date_of_birth ? \Carbon\Carbon::parse($p->date_of_birth)->age : null;
                $display = trim("{$p->first_name} {$p->last_name}");

                $ageSuffix = $is_es ? ' años' : ' yrs';
                if ($age !== null) {
                    $display .= " ({$age}{$ageSuffix})";
                }

                if ($p->gender) {
                    $genderStr = ucfirst($p->gender);
                    if ($is_es) {
                        $genderStr = match (strtolower($p->gender)) {
                            'male' => 'Masculino',
                            'female' => 'Femenino',
                            default => ucfirst($p->gender)
                        };
                    }
                    $display .= " • " . $genderStr;
                }

                if (mb_strlen($display) > 30) {
                    $display = mb_substr($display, 0, 27) . '...';
                }

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
                $response['data']['error_message'] = ($data['preferred_language'] ?? 'en') === 'es' ? 'Por favor seleccione un paciente' : 'Please select a patient';
                return $response;
            }

            $patient = Patient::where('id', $selectedId)
                ->where('is_active', true)
                ->where('is_deleted', false)
                ->first();

            if (!$patient) {
                $response['data']['error_message'] = ($data['preferred_language'] ?? 'en') === 'es' ? 'El paciente seleccionado no se encontró o está inactivo' : 'Selected patient not found or inactive';
                return $response;
            }

            $response['screen'] = 'PREFERRED_TIME';
            $response['data']['patient'] = (string) $patient->id;
            $is_es = ($data['preferred_language'] ?? 'en') === 'es';
            $genderStr = ucfirst($patient->gender);
            if ($is_es) {
                $genderStr = match (strtolower($patient->gender)) {
                    'male' => 'Masculino',
                    'female' => 'Femenino',
                    default => ucfirst($patient->gender)
                };
            }
            $patientLabel = $is_es ? "Paciente" : "Patient";
            $phoneLabel = $is_es ? "Teléfono" : "Phone";
            $genderLabel = $is_es ? "Género" : "Gender";

            $response['data']['patient_details'] = "{$patientLabel}: {$patient->first_name} {$patient->last_name}\n{$phoneLabel}: {$patient->phone}\n{$genderLabel}: " . $genderStr;
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
                'gender' => 'required|in:male,female',
                'address' => 'nullable|string|max:500',
                'previous_psychotherapy' => 'nullable|in:yes,no',
                'preferred_session_time' => 'nullable|string|max:255',
                'referred_by' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                $response['data']['error_message'] = implode(', ', $validator->errors()->all());
                return $response;
            }

            do {
                $mrn = 'MRN' . mt_rand(1000000, 9999999);
            } while (\App\Models\Patient::where('medical_record_number', $mrn)->exists());
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
            $is_es = ($data['preferred_language'] ?? 'en') === 'es';
            $genderStr = ucfirst($patient->gender);
            if ($is_es) {
                $genderStr = match (strtolower($patient->gender)) {
                    'male' => 'Masculino',
                    'female' => 'Femenino',
                    default => ucfirst($patient->gender)
                };
            }
            $patientLabel = $is_es ? "Paciente" : "Patient";
            $phoneLabel = $is_es ? "Teléfono" : "Phone";
            $genderLabel = $is_es ? "Género" : "Gender";

            $response['data']['patient_details'] = "{$patientLabel}: {$patient->first_name} {$patient->last_name}\n{$phoneLabel}: {$patient->phone}\n{$genderLabel}: " . $genderStr;
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
                $response['data']['error_message'] = ($data['preferred_language'] ?? 'en') === 'es' ? 'Por favor seleccione una opción de hora preferida' : 'Please select preferred time option';
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
            $is_es = ($data['preferred_language'] ?? 'en') === 'es';

            $patientLabel = $is_es ? "👤 Paciente:" : "👤 Patient:";
            $phoneLabel = $is_es ? "📱 Teléfono:" : "📱 Phone:";
            $therapistLabel = $is_es ? "🧑‍⚕️ Terapeuta:" : "🧑‍⚕️ Therapist:";
            $deptLabel = $is_es ? "🏥 Departamento:" : "🏥 Department:";
            $timeLabel = $is_es ? "🕒 Hora Preferida:" : "🕒 Preferred Time:";
            $reasonLabel = $is_es ? "📝 Motivo:" : "📝 Reason:";
            $notesLabel = $is_es ? "📌 Notas:" : "📌 Notes:";

            $patient = \App\Models\Patient::find($this->normalizeId($data['patient'] ?? null));
            if ($patient) {
                $summary[] = "{$patientLabel} " . $patient->first_name . " " . $patient->last_name;
                $summary[] = "{$phoneLabel} " . $patient->phone;
            }

            if (($data['appointment_type'] ?? '') === 'specific' && !empty($data['doctor'])) {
                $doc = \App\Models\Doctor::find($this->normalizeId($data['doctor']));
                if ($doc)
                    $summary[] = "{$therapistLabel} " . $doc->full_name;
            } elseif (($data['appointment_type'] ?? '') === 'any' && !empty($data['specialization'])) {
                $spec = \App\Models\Specialization::find($this->normalizeId($data['specialization']));
                if ($spec)
                    $summary[] = "{$deptLabel} " . $spec->name;
            }

            if ($is_es) {
                $prefTimeMap = [
                    'next' => 'Lo más pronto posible',
                    '7days' => 'Dentro de los próximos 7 días',
                    '15days' => 'Dentro de los próximos 15 días',
                ];
            } else {
                $prefTimeMap = [
                    'next' => 'Next available slot',
                    '7days' => 'Within next 7 days',
                    '15days' => 'Within next 15 days',
                ];
            }
            $prefTime = $prefTimeMap[$data['preferred_time'] ?? ''] ?? $prefTimeMap['next'];
            $summary[] = "{$timeLabel} " . $prefTime;

            $summary[] = "{$reasonLabel} " . $data['reason'];

            if (!empty($data['notes'])) {
                $summary[] = "{$notesLabel} " . $data['notes'];
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
                $response['data']['error_message'] = ($data['preferred_language'] ?? 'en') === 'es' ? 'Terapeuta requerido' : 'Therapist is required';
                return $response;
            }
            if ($data['appointment_type'] === 'any' && $specializationId === null) {
                $response['data']['error_message'] = ($data['preferred_language'] ?? 'en') === 'es' ? 'Especialidad requerida' : 'Specialization is required';
                return $response;
            }

            $langSlug = $data['preferred_language'] ?? 'en';
            $langOption = \App\Models\OptionList::where('type', 'language')->where('slug', $langSlug)->first();

            // Prevent duplicates if user hits back and submits again
            $recentAppointment = Appointment::where('patient_id', $data['patient'])
                ->where('appointment_type', $data['appointment_type'])
                ->where('doctor_id', $doctorId)
                ->where('specialization_id', $specializationId)
                ->where('reason_for_visit', $data['reason'])
                ->where('created_at', '>=', now()->subMinutes(5))
                ->first();

            if ($recentAppointment) {
                // If they go back and try to submit again, show a friendly validation error
                $response['data']['error_message'] = ($data['preferred_language'] ?? 'en') === 'es' ? 'Esta cita ya ha sido confirmada.' : 'This appointment has already been confirmed.';
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
                'age_group_id' => $ageGroupId,
                'preferred_language_id' => $langOption ? $langOption->id : null,
                'preferred_time' => $data['preferred_time'] ?? null,
            ]);

            $recipients = User::role(['admin', 'receptionist'])->get();
            Notification::send($recipients, new NewAppointmentCreated($appointment));

            $response['screen'] = 'SUCCESS';
            $is_es = ($data['preferred_language'] ?? 'en') === 'es';
            if ($is_es) {
                $prefTimeMap = [
                    'next' => 'Lo más pronto posible',
                    '7days' => 'Dentro de los próximos 7 días',
                    '15days' => 'Dentro de los próximos 15 días',
                ];
            } else {
                $prefTimeMap = [
                    'next' => 'Next available slot',
                    '7days' => 'Within next 7 days',
                    '15days' => 'Within next 15 days',
                ];
            }
            $humanPrefTime = $prefTimeMap[$data['preferred_time'] ?? ''] ?? $prefTimeMap['next'];

            $response['data'] = [
                'appointment_id' => (string) $appointment->id,
                'success_message' => $is_es
                    ? '¡Su solicitud de cita ha sido enviada con éxito! Nuestro equipo se comunicará con usted pronto.'
                    : 'Your appointment request has been submitted successfully! Our team will contact you soon.',
                'details_text' => ($is_es ? "Motivo: " : "Reason: ") . ($data['reason'] ?? ($is_es ? 'No especificado' : 'Not specified'))
                    . ($is_es ? "\nHora Preferida: " : "\nPreferred Time: ") . $humanPrefTime
                    . (!empty($data['notes']) ? ($is_es ? "\nNotas: " : "\nNotes: ") . $data['notes'] : ''),
                'error_message' => '',
            ];

            return $response;
        }

        $response['data']['error_message'] = ($data['preferred_language'] ?? 'en') === 'es' ? 'Acción o pantalla no válida' : 'Invalid action or screen';
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
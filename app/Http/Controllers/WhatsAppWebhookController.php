<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppService;
use App\Models\WhatsappConversation; // Create this model
use App\Models\Patient;
use App\Models\Specialization;
use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class WhatsAppWebhookController extends Controller
{
    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function handle(Request $request)
    {
        // Verification (GET request from Meta)
        if ($request->isMethod('get')) {
            if ($request->hub_mode === 'subscribe' && $request->hub_verify_token === config('whatsapp.verify_token')) {
                return response($request->hub_challenge, 200);
            }
            return response('Invalid verification', 403);
        }

        // Incoming message (POST)
        $payload = $request->all();

        if (empty($payload['entry'][0]['changes'][0]['value']['messages'][0])) {
            return response('No message', 200);
        }

        $messageData = $payload['entry'][0]['changes'][0]['value']['messages'][0];
        $from = $messageData['from']; // User's WhatsApp number (with country code, no +)
        $type = $messageData['type'] ?? 'text';

        // Handle different message types
        if ($type === 'text') {
            $text = $messageData['text']['body'];
            $this->processTextMessage($from, $text);
        } elseif ($type === 'interactive') {
            // Handle button/list replies
            $interactive = $messageData['interactive'];
            if ($interactive['type'] === 'button_reply') {
                $buttonId = $interactive['button_reply']['id'];
                $this->processButtonReply($from, $buttonId);
            } elseif ($interactive['type'] === 'list_reply') {
                $listId = $interactive['list_reply']['id'];
                $this->processListReply($from, $listId);
            }
        }

        return response('OK', 200);
    }

    protected function processTextMessage(string $phone, string $text)
    {
        $conv = WhatsappConversation::updateOrCreate(
            ['phone' => $phone],
            ['last_active' => now()]
        );

        $step = $conv->step ?? 'start';
        $data = $conv->data ?? [];

        $text = strtolower(trim($text));

        if ($step === 'start' || $text === 'hi' || $text === 'book' || Str::contains($text, 'appointment')) {
            $this->whatsapp->sendButtons($phone, "Hi! 👋 Do you want to book an appointment?", [
                ['id' => 'book_now', 'title' => 'Book Now'],
                ['id' => 'help', 'title' => 'Help'],
            ], 'Appointment Booking');

            $conv->update(['step' => 'choose_type', 'data' => []]);
            return;
        }

        // Handle other steps: reason, notes, date, etc. (open text)
        if ($step === 'reason') {
            $data['reason_for_visit'] = $text;
            $conv->update(['data' => $data, 'step' => 'notes']);
            $this->whatsapp->sendText($phone, "Any additional notes? (reply 'skip' to skip)");
            return;
        }

        // ... add more steps
    }

    protected function processButtonReply(string $phone, string $buttonId)
    {
        $conv = WhatsappConversation::where('phone', $phone)->first();
        if (!$conv) return;

        $data = $conv->data ?? [];

        if ($buttonId === 'book_now') {
            $this->whatsapp->sendButtons($phone, "What type of appointment?", [
                ['id' => 'specific', 'title' => 'Specific Doctor'],
                ['id' => 'any', 'title' => 'Any Doctor'],
                ['id' => 'primary', 'title' => 'Primary Provider'],
            ]);

            $conv->update(['step' => 'appointment_type']);
            return;
        }

        if (Str::startsWith($buttonId, 'type_')) {
            $type = Str::after($buttonId, 'type_');
            $data['appointment_type'] = $type;

            if ($type === 'specific') {
                // Fetch doctors → send list
                $doctors = Doctor::orderBy('last_name')->get()->map(function ($doc) {
                    return ['id' => 'doc_' . $doc->id, 'title' => $doc->full_name, 'description' => $doc->specialization->name ?? ''];
                })->take(10);

                $this->whatsapp->sendList($phone, 'Select Doctor', 'Choose a doctor:', [
                    'Doctors' => $doctors->toArray(),
                ]);

                $conv->update(['step' => 'select_doctor', 'data' => $data]);
            } elseif ($type === 'any') {
                // Send specializations list
                $specializations = Specialization::orderBy('name')->get()->map(fn($s) => [
                    'id' => 'spec_' . $s->id,
                    'title' => $s->name,
                ]);

                $this->whatsapp->sendList($phone, 'Select Specialty', 'Choose a specialty:', [
                    'Specialties' => $specializations->toArray(),
                ]);

                $conv->update(['step' => 'select_specialization', 'data' => $data]);
            } else {
                // Primary provider logic (e.g. find patient's primary doctor)
                $conv->update(['step' => 'reason', 'data' => $data]);
                $this->whatsapp->sendText($phone, "Please tell me the reason for the visit:");
            }
        }

        // ... handle more button IDs
    }

    protected function processListReply(string $phone, string $listId)
    {
        $conv = WhatsappConversation::where('phone', $phone)->firstOrFail();
        $data = $conv->data ?? [];

        if (Str::startsWith($listId, 'doc_')) {
            $doctorId = Str::after($listId, 'doc_');
            $data['doctor_id'] = $doctorId;
            $conv->update(['data' => $data, 'step' => 'reason']);
            $this->whatsapp->sendText($phone, "Please tell me the reason for the visit (max 1000 chars):");
        } elseif (Str::startsWith($listId, 'spec_')) {
            $specId = Str::after($listId, 'spec_');
            $data['specialization_id'] = $specId;
            $conv->update(['data' => $data, 'step' => 'reason']);
            $this->whatsapp->sendText($phone, "Please tell me the reason for the visit:");
        }

        // When all collected → create appointment
        if ($conv->step === 'confirm') {
            $this->createAppointment($phone, $data);
        }
    }

    protected function createAppointment(string $phone, array $data)
    {
        // Map to your validation/create logic
        // Find patient by phone or ask for more info if needed
        // For simplicity assume you have a way to link phone → patient_id

        $appointmentData = [
            'patient_id'       => $data['patient_id'] ?? 1, // ← implement lookup
            'appointment_type' => $data['appointment_type'],
            'reason_for_visit' => $data['reason_for_visit'] ?? 'Not provided',
            'patient_notes'    => $data['patient_notes'] ?? null,
            'status'           => Appointment::STATUS_PENDING,
            'specialization_id' => $data['specialization_id'] ?? null,
            'doctor_id'        => $data['doctor_id'] ?? null,
        ];

        if (!empty($data['scheduled_start'])) {
            $start = Carbon::parse($data['scheduled_start']);
            $appointmentData['scheduled_start'] = $start;
            $appointmentData['scheduled_end'] = $start->copy()->addMinutes(30);
        }

        $appointment = Appointment::create($appointmentData);

        // Notify admins (your existing logic)
        // ...

        $this->whatsapp->sendText($phone, "Appointment request created! Reference: #{$appointment->id}\nWe'll confirm soon.");

        // Reset conversation
        WhatsappConversation::where('phone', $phone)->update(['step' => 'start', 'data' => []]);
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppSetting;
use Illuminate\Http\Request;

class WhatsAppManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:settings.edit');
    }

    public function index()
    {
        $settings = [
            'assign_enabled' => WhatsAppSetting::getByKey('whatsapp_assign_enabled', true),
            'assign_template' => WhatsAppSetting::getByKey('whatsapp_assign_template', "Hi {patient_name}, your appointment with {doctor_name} has been scheduled for {date} at {time}. Reference: #{appointment_number}."),
            'reschedule_enabled' => WhatsAppSetting::getByKey('whatsapp_reschedule_enabled', true),
            'reschedule_template' => WhatsAppSetting::getByKey('whatsapp_reschedule_template', "Hi {patient_name}, your appointment with {doctor_name} has been rescheduled to {date} at {time}. Reference: #{appointment_number}."),
        ];

        return view('admin.whatsapp.index', compact('settings'));
    }

    public function historyDatatable(Request $request)
    {
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = trim($request->input('search.value', ''));

        $query = WhatsAppMessage::with(['appointment.patient', 'appointment.doctor']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('phone_number', 'like', "%{$search}%")
                  ->orWhere('message_content', 'like', "%{$search}%")
                  ->orWhereHas('appointment.patient', function($sq) use ($search) {
                      $sq->whereRaw("CONCAT_WS(' ', first_name, NULLIF(middle_name, ''), last_name) LIKE ?", ["%{$search}%"]);
                  });
            });
        }

        $total = (clone $query)->count();
        $filtered = $total;

        $rows = $query->latest()->offset($start)->limit($length)->get();

        $data = $rows->map(function($r) {
            $statusBadge = match ($r->status) {
                'sent' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">' . (__('file.sent') ?? 'Sent') . '</span>',
                'failed' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">' . (__('file.failed') ?? 'Failed') . '</span>',
                'skipped' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">' . (__('file.skipped') ?? 'Skipped') . '</span>',
                default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">' . ucfirst(__("file.{$r->status}") ?? $r->status) . '</span>',
            };

            return [
                'id' => $r->id,
                'patient' => $r->appointment?->patient?->full_name ?? 'N/A',
                'phone' => $r->phone_number,
                'type' => ucfirst(__("file.{$r->message_type}") ?? str_replace('_', ' ', $r->message_type)),
                'content' => e($r->message_content),
                'status' => $statusBadge,
                'error' => $r->error_message ? '<span class="text-xs text-red-500">' . e($r->error_message) . '</span>' : '—',
                'created_at' => $r->created_at->translatedFormat('M d, Y H:i'),
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data->toArray(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'whatsapp_assign_enabled' => 'nullable|boolean',
            'whatsapp_assign_template' => 'required|string',
            'whatsapp_reschedule_enabled' => 'nullable|boolean',
            'whatsapp_reschedule_template' => 'required|string',
        ]);

        WhatsAppSetting::setByKey('whatsapp_assign_enabled', $request->has('whatsapp_assign_enabled') ? '1' : '0', 'boolean');
        WhatsAppSetting::setByKey('whatsapp_assign_template', $validated['whatsapp_assign_template']);
        WhatsAppSetting::setByKey('whatsapp_reschedule_enabled', $request->has('whatsapp_reschedule_enabled') ? '1' : '0', 'boolean');
        WhatsAppSetting::setByKey('whatsapp_reschedule_template', $validated['whatsapp_reschedule_template']);

        return back()->with('success', __('file.settings_updated_successfully'));
    }
}

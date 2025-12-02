<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentSlotController extends Controller
{
    public function index(Request $request)
    {
        $doctorId = $request->query('doctor_id');
        $date     = $request->query('date');

        if (!$doctorId || !$date) {
            return response()->json([]);
        }

        $date  = Carbon::parse($date);
        $start = $date->copy()->setTime(8, 0);   // 8:00 AM
        $end   = $date->copy()->setTime(18, 0);  // 6:00 PM
        $slots = [];

        while ($start < $end) {
            $slotTime = $start->format('H:i');
            $slotEnd  = $start->copy()->addMinutes(30);

            $isBooked = Appointment::where('doctor_id', $doctorId)
                ->where('appointment_datetime', '>=', $start)
                ->where('appointment_datetime', '<', $slotEnd)
                ->exists();

            if (! $isBooked) {
                $slots[] = $slotTime;
            }

            $start->addMinutes(30);
        }

        return response()->json($slots);
    }
}
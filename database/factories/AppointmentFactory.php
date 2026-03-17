<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => Doctor::factory(),
            'status' => Appointment::STATUS_PENDING,
            'appointment_type' => Appointment::TYPE_SPECIFIC,
            'reason_for_visit' => $this->faker->sentence(),
            'scheduled_start' => $this->faker->dateTimeBetween('now', '+1 month'),
            'scheduled_end' => function (array $attributes) {
                return \Carbon\Carbon::parse($attributes['scheduled_start'])->addMinutes(30);
            },
        ];
    }
}

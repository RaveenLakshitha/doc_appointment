<?php

namespace Database\Factories;

use App\Models\AppointmentRequest;
use App\Models\Patient;
use App\Models\Specialization;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AppointmentRequest::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'patient_id'            => Patient::inRandomOrder()->first()?->id,
            'specialization_id'     => Specialization::inRandomOrder()->first()?->id,
            'doctor_selection_mode' => $this->faker->randomElement([
                AppointmentRequest::DOCTOR_SELECTION_SPECIFIC,
                AppointmentRequest::DOCTOR_SELECTION_ANY,
                AppointmentRequest::DOCTOR_SELECTION_PRIMARY_PROVIDER,
            ]),
            'reason_for_visit'      => $this->faker->sentence(),
            'notes'                 => $this->faker->optional(0.5)->paragraph(),
            'duration_minutes'      => $this->faker->randomElement([30, 45, 60]),
            'status'                => $this->faker->randomElement(['pending', 'approved', 'rejected', 'cancelled']),
            'created_at'            => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at'            => now(),
        ];
    }
}
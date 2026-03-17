<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'       => \App\Models\User::factory(),
            'first_name'    => $this->faker->firstName(),
            'middle_name'   => $this->faker->optional()->firstName(),
            'last_name'     => $this->faker->lastName(),
            'email'         => $this->faker->unique()->safeEmail(),
            'phone'         => $this->faker->phoneNumber(),
            'is_active'     => 1,
            'department_id' => \App\Models\Department::factory(),
            'primary_specialization_id' => \App\Models\Specialization::factory(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }

    // Optional: state for active doctors
    public function active()
    {
        return $this->state(fn () => ['active' => 1]); // adjust field name
    }
}
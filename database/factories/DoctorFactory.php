<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name'    => $this->faker->firstName(),
            'middle_name'   => $this->faker->optional()->firstName(),
            'last_name'     => $this->faker->lastName(),
            // add 'active' => 1, 'email', etc. if your Doctor model needs them
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
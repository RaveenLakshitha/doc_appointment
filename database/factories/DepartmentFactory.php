<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'           => $this->faker->unique()->words(asText: true),
            'status'         => $this->faker->boolean(),
            'head_doctor_id' => null,
            'location'       => $this->faker->city(),
            'email'          => $this->faker->safeEmail(),
            'phone'          => $this->faker->phoneNumber(),
            'description'    => $this->faker->paragraph(),
        ];
    }
}
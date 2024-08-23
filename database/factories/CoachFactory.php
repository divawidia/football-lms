<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coach>
 */
class CoachFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'userId' => User::factory(),
            'certificationLevel' => 'UEFA Pro',
            'specialization' => 'Tactic',
            'height' => fake()->numberBetween(160,180),
            'weight' => fake()->numberBetween(40,70),
            'hireDate' => fake()->date,
        ];
    }
}

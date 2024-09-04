<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
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
            'positionId' => 1,
            'skill' => 'Beginner',
            'strongFoot' => 'left',
            'height' => fake()->numberBetween(150,170),
            'weight' => fake()->numberBetween(40,70),
            'joinDate' => fake()->date,
        ];
    }
}

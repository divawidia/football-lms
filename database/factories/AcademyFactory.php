<?php

namespace Database\Factories;


use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AcademyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'phoneNumber' => fake()->phoneNumber,
            'academyName' => fake()->name,
            'address' => fake()->address,
            'state_id' => 1,
            'city_id' => 1,
            'country_id' => 1,
            'zipCode' => fake()->postcode,
            'directorName' => fake()->name,
            'status' => 1,
            'logo' => 'images/undefined-user.png',
            'academyDescription' => fake()->text
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'iso2' => 'US',
            'name' => fake('en_US')->country ,
            'status' => 1,
            'phone_code' => 1,
            'iso3' => fake('en_US')->countryISOAlpha3,
            'region' => 'Americas',
            'subregion' => 'Northern America',
        ];
    }
}

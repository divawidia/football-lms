<?php

namespace Database\Factories;

use App\Models\Academy;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'firstName' => fake()->firstName,
            'lastName' => fake()->lastName,
            'foto' => 'images/undefined-user.png',
            'dob' => fake()->date,
            'gender' => 'male',
            'address' => fake()->address,
            'state_id' => State::factory(),
            'city_id' => City::factory(),
            'country_id' => Country::factory(),
            'zipCode' => fake()->postcode,
            'phoneNumber' => fake()->phoneNumber,
            'status' => 1,
            'academyId' => Academy::factory(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

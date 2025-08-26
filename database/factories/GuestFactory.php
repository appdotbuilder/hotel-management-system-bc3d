<?php

namespace Database\Factories;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guest>
 */
class GuestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\Guest>
     */
    protected $model = Guest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'id_number' => $this->faker->optional(0.8)->numerify('##########'),
            'date_of_birth' => $this->faker->optional(0.7)->date('Y-m-d', '-18 years'),
            'gender' => $this->faker->optional(0.6)->randomElement(['male', 'female', 'other']),
            'nationality' => $this->faker->optional(0.5)->country(),
            'preferences' => $this->faker->optional(0.3)->sentence(),
        ];
    }
}
<?php

namespace Database\Factories;

use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\Reservation>
     */
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkInDate = $this->faker->dateTimeBetween('-30 days', '+60 days');
        $checkOutDate = $this->faker->dateTimeBetween($checkInDate, $checkInDate->format('Y-m-d') . ' +7 days');
        
        // Calculate nights and total amount
        $roomType = RoomType::inRandomOrder()->first() ?? RoomType::factory()->create();
        $nights = $checkInDate->diff($checkOutDate)->days;
        $totalAmount = floatval($roomType->base_price) * $nights;

        return [
            'reservation_number' => 'RES-' . strtoupper($this->faker->unique()->lexify('??????')),
            'guest_id' => Guest::factory(),
            'room_id' => $this->faker->optional(0.8)->randomElement(Room::pluck('id')->toArray() ?: [null]),
            'room_type_id' => $roomType->id,
            'check_in_date' => $checkInDate->format('Y-m-d'),
            'check_out_date' => $checkOutDate->format('Y-m-d'),
            'adults' => $this->faker->numberBetween(1, 4),
            'children' => $this->faker->numberBetween(0, 2),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled']),
            'total_amount' => $totalAmount,
            'special_requests' => $this->faker->optional(0.4)->sentence(),
            'notes' => $this->faker->optional(0.3)->sentence(),
            'checked_in_at' => $this->faker->optional(0.3)->dateTimeBetween($checkInDate, $checkOutDate),
            'checked_out_at' => $this->faker->optional(0.2)->dateTimeBetween($checkInDate, $checkOutDate),
        ];
    }

    /**
     * Indicate that the reservation is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * Indicate that the guest is checked in.
     */
    public function checkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);
    }
}
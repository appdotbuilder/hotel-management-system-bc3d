<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservations_index_displays_correctly(): void
    {
        $user = User::factory()->create();
        $guest = Guest::factory()->create();
        $roomType = RoomType::factory()->create();
        $room = Room::factory()->create(['room_type_id' => $roomType->id]);
        
        Reservation::factory()->create([
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'room_type_id' => $roomType->id,
        ]);

        $response = $this->actingAs($user)->get('/reservations');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('reservations/index')
                ->has('reservations')
        );
    }

    public function test_reservation_can_be_created(): void
    {
        $user = User::factory()->create();
        $guest = Guest::factory()->create();
        $roomType = RoomType::factory()->create();

        $reservationData = [
            'guest_id' => $guest->id,
            'room_type_id' => $roomType->id,
            'check_in_date' => now()->addDay()->format('Y-m-d'),
            'check_out_date' => now()->addDays(3)->format('Y-m-d'),
            'adults' => 2,
            'children' => 0,
            'status' => 'confirmed',
        ];

        $response = $this->actingAs($user)->post('/reservations', $reservationData);

        $this->assertDatabaseHas('reservations', [
            'guest_id' => $guest->id,
            'room_type_id' => $roomType->id,
            'adults' => 2,
            'status' => 'confirmed',
        ]);

        $reservation = Reservation::where('guest_id', $guest->id)->first();
        $response->assertRedirect("/reservations/{$reservation->id}");
    }

    public function test_reservation_validation_works(): void
    {
        $user = User::factory()->create();

        $invalidData = [
            'guest_id' => 999, // Non-existent guest
            'room_type_id' => 999, // Non-existent room type
            'check_in_date' => now()->subDay()->format('Y-m-d'), // Past date
            'check_out_date' => now()->format('Y-m-d'), // Same as check-in
            'adults' => 0, // Invalid number
            'children' => -1, // Invalid number
            'status' => 'invalid_status', // Invalid status
        ];

        $response = $this->actingAs($user)->post('/reservations', $invalidData);

        $response->assertSessionHasErrors([
            'guest_id',
            'room_type_id',
            'check_in_date',
            'adults',
        ]);
    }

    public function test_reservation_can_be_updated(): void
    {
        $user = User::factory()->create();
        $guest = Guest::factory()->create();
        $roomType = RoomType::factory()->create();
        $room = Room::factory()->create(['room_type_id' => $roomType->id]);
        
        $reservation = Reservation::factory()->create([
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'room_type_id' => $roomType->id,
            'status' => 'confirmed',
        ]);

        $updateData = [
            'guest_id' => $guest->id,
            'room_type_id' => $roomType->id,
            'room_id' => $room->id,
            'check_in_date' => $reservation->check_in_date,
            'check_out_date' => $reservation->check_out_date,
            'adults' => 3, // Changed
            'children' => 1, // Changed
            'status' => 'checked_in', // Changed
        ];

        $response = $this->actingAs($user)->put("/reservations/{$reservation->id}", $updateData);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'adults' => 3,
            'children' => 1,
            'status' => 'checked_in',
        ]);

        $response->assertRedirect("/reservations/{$reservation->id}");
    }

    public function test_reservation_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $guest = Guest::factory()->create();
        $roomType = RoomType::factory()->create();
        
        $reservation = Reservation::factory()->create([
            'guest_id' => $guest->id,
            'room_type_id' => $roomType->id,
        ]);

        $response = $this->actingAs($user)->delete("/reservations/{$reservation->id}");

        $this->assertDatabaseMissing('reservations', [
            'id' => $reservation->id,
        ]);

        $response->assertRedirect('/reservations');
    }
}
<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\HousekeepingTask;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_hotel_dashboard_displays_correctly_for_authenticated_users(): void
    {
        // Create test data
        $user = User::factory()->create();
        $roomType = RoomType::factory()->create();
        $room = Room::factory()->create(['room_type_id' => $roomType->id]);
        $guest = Guest::factory()->create();
        
        Reservation::factory()->create([
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'room_type_id' => $roomType->id,
            'status' => 'confirmed'
        ]);

        HousekeepingTask::factory()->create([
            'room_id' => $room->id,
            'status' => 'pending'
        ]);

        // Test authenticated access
        $response = $this->actingAs($user)->get('/hotel-dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('hotel-dashboard')
                ->has('stats')
                ->has('recent_reservations')
                ->has('pending_tasks')
                ->has('rooms_by_type')
        );
    }

    public function test_unauthenticated_users_cannot_access_hotel_dashboard(): void
    {
        $response = $this->get('/hotel-dashboard');

        $response->assertRedirect('/login');
    }

    public function test_room_availability_search_works(): void
    {
        $user = User::factory()->create();
        $roomType = RoomType::factory()->create();
        Room::factory()->available()->create(['room_type_id' => $roomType->id]);

        $searchData = [
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(3)->format('Y-m-d'),
            'guests' => 2,
            'room_type_id' => $roomType->id,
        ];

        $response = $this->actingAs($user)->post('/hotel-dashboard', $searchData);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('hotel-dashboard')
                ->has('search_results')
                ->has('search_params')
        );
    }

    public function test_dashboard_redirect_works(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/hotel-dashboard');
    }
}
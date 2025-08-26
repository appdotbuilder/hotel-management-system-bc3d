<?php

namespace Database\Seeders;

use App\Models\Guest;
use App\Models\HousekeepingTask;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create room types first
        $roomTypes = [
            [
                'name' => 'Standard Single',
                'description' => 'Comfortable single room with basic amenities',
                'base_price' => 89.99,
                'max_occupancy' => 1,
                'amenities' => ['Wi-Fi', 'AC', 'TV', 'Desk']
            ],
            [
                'name' => 'Standard Double',
                'description' => 'Spacious double room perfect for couples',
                'base_price' => 129.99,
                'max_occupancy' => 2,
                'amenities' => ['Wi-Fi', 'AC', 'TV', 'Mini Fridge', 'Coffee Maker']
            ],
            [
                'name' => 'Deluxe Suite',
                'description' => 'Luxurious suite with separate living area',
                'base_price' => 249.99,
                'max_occupancy' => 4,
                'amenities' => ['Wi-Fi', 'AC', 'Smart TV', 'Mini Bar', 'Balcony', 'Room Service']
            ],
            [
                'name' => 'Executive Room',
                'description' => 'Business-class room with workspace',
                'base_price' => 189.99,
                'max_occupancy' => 2,
                'amenities' => ['Wi-Fi', 'AC', 'TV', 'Work Desk', 'Business Center Access']
            ],
        ];

        foreach ($roomTypes as $roomTypeData) {
            RoomType::create($roomTypeData);
        }

        // Create rooms
        $roomTypes = RoomType::all();
        $roomNumber = 101;

        foreach (range(1, 5) as $floor) {
            foreach ($roomTypes as $roomType) {
                for ($i = 0; $i < 3; $i++) {
                    Room::create([
                        'room_number' => (string) $roomNumber,
                        'room_type_id' => $roomType->id,
                        'floor' => (string) $floor,
                        'status' => fake()->randomElement(['available', 'available', 'available', 'occupied', 'maintenance']),
                        'notes' => fake()->optional(0.2)->sentence(),
                    ]);
                    $roomNumber++;
                }
            }
        }

        // Create guests
        Guest::factory(50)->create();

        // Create reservations
        $guests = Guest::all();
        $rooms = Room::all();
        $roomTypes = RoomType::all();

        foreach (range(1, 30) as $i) {
            $checkInDate = fake()->dateTimeBetween('-15 days', '+30 days');
            $checkOutDate = fake()->dateTimeBetween($checkInDate, $checkInDate->format('Y-m-d') . ' +7 days');
            $roomType = $roomTypes->random();
            $nights = $checkInDate->diff($checkOutDate)->days;

            Reservation::create([
                'reservation_number' => 'RES-' . strtoupper(fake()->unique()->lexify('??????')),
                'guest_id' => $guests->random()->id,
                'room_id' => fake()->optional(0.8)->randomElement($rooms->pluck('id')->toArray()),
                'room_type_id' => $roomType->id,
                'check_in_date' => $checkInDate->format('Y-m-d'),
                'check_out_date' => $checkOutDate->format('Y-m-d'),
                'adults' => fake()->numberBetween(1, $roomType->max_occupancy),
                'children' => fake()->numberBetween(0, 2),
                'status' => fake()->randomElement(['confirmed', 'confirmed', 'checked_in', 'checked_out', 'cancelled']),
                'total_amount' => floatval($roomType->base_price) * $nights,
                'special_requests' => fake()->optional(0.3)->sentence(),
                'notes' => fake()->optional(0.2)->sentence(),
                'checked_in_at' => fake()->optional(0.4)->dateTimeBetween($checkInDate, $checkOutDate),
                'checked_out_at' => fake()->optional(0.2)->dateTimeBetween($checkInDate, $checkOutDate),
            ]);
        }

        // Create housekeeping tasks
        $taskDescriptions = [
            'cleaning' => [
                'Deep clean bathroom and replace towels',
                'Vacuum carpets and mop floors',
                'Change bed linens and pillowcases',
                'Clean windows and dust furniture'
            ],
            'maintenance' => [
                'Fix leaky faucet in bathroom',
                'Replace burnt out light bulb',
                'Repair air conditioning unit',
                'Fix broken door handle'
            ],
            'inspection' => [
                'Routine room inspection for damages',
                'Check all electrical outlets and appliances',
                'Inspect plumbing for leaks',
                'Verify room inventory is complete'
            ],
            'restocking' => [
                'Restock mini bar items',
                'Replace toiletries and amenities',
                'Add fresh coffee and tea supplies',
                'Restock cleaning supplies in room'
            ]
        ];

        foreach (range(1, 25) as $i) {
            $taskType = fake()->randomElement(['cleaning', 'maintenance', 'inspection', 'restocking']);
            $status = fake()->randomElement(['pending', 'pending', 'in_progress', 'completed']);
            $scheduledAt = fake()->optional(0.8)->dateTimeBetween('now', '+2 days');
            
            $startedAt = null;
            $completedAt = null;
            
            if ($status === 'in_progress') {
                $startedAt = fake()->dateTimeBetween('-2 hours', 'now');
            } elseif ($status === 'completed') {
                $startedAt = fake()->dateTimeBetween('-1 day', '-2 hours');
                $completedAt = fake()->dateTimeBetween($startedAt, 'now');
            }

            HousekeepingTask::create([
                'room_id' => $rooms->random()->id,
                'task_type' => $taskType,
                'priority' => fake()->randomElement(['low', 'medium', 'high']),
                'description' => fake()->randomElement($taskDescriptions[$taskType]),
                'status' => $status,
                'assigned_to' => fake()->optional(0.7)->randomElement([1]), // Assuming user ID 1 exists
                'scheduled_at' => $scheduledAt,
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
                'completion_notes' => $status === 'completed' ? fake()->optional(0.4)->sentence() : null,
            ]);
        }
    }
}
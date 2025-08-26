<?php

namespace Database\Factories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomType>
 */
class RoomTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\RoomType>
     */
    protected $model = RoomType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
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

        static $index = 0;
        $roomType = $roomTypes[$index % count($roomTypes)];
        $index++;

        return $roomType;
    }
}
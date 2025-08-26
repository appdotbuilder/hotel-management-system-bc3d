<?php

namespace Database\Factories;

use App\Models\HousekeepingTask;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HousekeepingTask>
 */
class HousekeepingTaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\HousekeepingTask>
     */
    protected $model = HousekeepingTask::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $taskType = $this->faker->randomElement(['cleaning', 'maintenance', 'inspection', 'restocking']);
        
        $descriptions = [
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

        $scheduledAt = $this->faker->optional(0.7)->dateTimeBetween('now', '+3 days');
        $status = $this->faker->randomElement(['pending', 'in_progress', 'completed']);
        
        $startedAt = null;
        $completedAt = null;
        
        if ($status === 'in_progress') {
            $startedAt = $this->faker->dateTimeBetween('-2 hours', 'now');
        } elseif ($status === 'completed') {
            $startedAt = $this->faker->dateTimeBetween('-1 day', '-2 hours');
            $completedAt = $this->faker->dateTimeBetween($startedAt, 'now');
        }

        return [
            'room_id' => Room::factory(),
            'task_type' => $taskType,
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'description' => $this->faker->randomElement($descriptions[$taskType]),
            'status' => $status,
            'assigned_to' => $this->faker->optional(0.6)->randomElement(User::pluck('id')->toArray() ?: [null]),
            'scheduled_at' => $scheduledAt,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            'completion_notes' => $status === 'completed' ? $this->faker->optional(0.5)->sentence() : null,
        ];
    }

    /**
     * Indicate that the task is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'started_at' => null,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the task is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }
}
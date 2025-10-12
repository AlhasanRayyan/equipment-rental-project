<?php

namespace Database\Factories;

use App\Models\EquipmentTracking;
use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EquipmentTracking>
 */
class EquipmentTrackingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EquipmentTracking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure there is at least one equipment that has GPS tracker
        // No change here, `hasGpsTracker` state is sufficient
        $equipment = Equipment::where('has_gps_tracker', true)->inRandomOrder()->first() ??
                     Equipment::factory()->hasGpsTracker()->create();

        return [
            'equipment_id' => $equipment->id,
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'speed' => $this->faker->randomFloat(2, 0, 100),
            'battery_level' => $this->faker->randomFloat(2, 0, 100),
            'status' => $this->faker->randomElement(['online', 'offline', 'moving', 'idle']),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the device is online.
     */
    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'online',
        ]);
    }

    /**
     * Indicate that the device is offline.
     */
    public function offline(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'offline',
            'speed' => 0.00,
        ]);
    }

    /**
     * Indicate that the device is moving.
     */
    public function moving(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'moving',
            'speed' => $this->faker->randomFloat(2, 1, 80),
        ]);
    }

    /**
     * Indicate that the device is idle (online but not moving).
     */
    public function idle(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'idle',
            'speed' => 0.00,
        ]);
    }

    /**
     * Indicate a low battery level.
     */
    public function lowBattery(): static
    {
        return $this->state(fn (array $attributes) => [
            'battery_level' => $this->faker->randomFloat(2, 5, 15),
        ]);
    }

    /**
     * Indicate full battery level.
     */
    public function fullBattery(): static
    {
        return $this->state(fn (array $attributes) => [
            'battery_level' => $this->faker->randomFloat(2, 90, 100),
        ]);
    }

    /**
     * Attach tracking record to a specific equipment.
     */
    public function forEquipment(Equipment $equipment): static
    {
        return $this->state(fn (array $attributes) => [
            'equipment_id' => $equipment->id,
        ]);
    }
}
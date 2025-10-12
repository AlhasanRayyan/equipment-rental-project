<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\User;
use App\Models\EquipmentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Equipment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure there are existing regular users to act as owners
        $owner = User::where('role', 'user')->inRandomOrder()->first() ?? User::factory()->regularUser()->create();
        $category = EquipmentCategory::inRandomOrder()->first() ?? EquipmentCategory::factory()->create(); // Changed to get existing category or create one

        return [
            'owner_id' => $owner->id,
            'category_id' => $category->id,
            // تم التعديل هنا: حذفنا كلا استدعاءي ->unique()
            // لأن Laravel factory سيعالج فرادة الاسم بالكامل ضد قاعدة البيانات (حيث حقل name هو unique).
            'name' => $this->faker->words(3, true) . ' ' . $this->faker->randomElement(['Excavator', 'Drill', 'Tractor', 'Crane', 'Generator', 'Saw', 'Ladder']),
            'description' => $this->faker->paragraph(),
            'daily_rate' => $this->faker->randomFloat(2, 50, 500),
            'weekly_rate' => $this->faker->optional(0.7)->randomFloat(2, 200, 2000),
            'monthly_rate' => $this->faker->optional(0.5)->randomFloat(2, 800, 8000),
            'deposit_amount' => $this->faker->randomFloat(2, 100, 1000),
            'location_latitude' => $this->faker->latitude(),
            'location_longitude' => $this->faker->longitude(),
            'location_address' => $this->faker->address(),
            'status' => $this->faker->randomElement(['available', 'rented', 'maintenance', 'unavailable']),
            'is_approved_by_admin' => $this->faker->boolean(80),
            'has_gps_tracker' => $this->faker->boolean(30),
            'average_rating' => $this->faker->randomFloat(2, 1, 5),
            'total_reviews' => $this->faker->numberBetween(0, 100),
            'last_maintenance_date' => $this->faker->optional(0.7)->dateTimeBetween('-2 years', 'now'),
            'maintenance_notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the equipment is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'available',
        ]);
    }

    /**
     * Indicate that the equipment is rented.
     */
    public function rented(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rented',
        ]);
    }

    /**
     * Indicate that the equipment is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved_by_admin' => true,
        ]);
    }

    /**
     * Indicate that the equipment is unapproved.
     */
    public function unapproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved_by_admin' => false,
        ]);
    }

    /**
     * Indicate that the equipment has a GPS tracker.
     */
    public function hasGpsTracker(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_gps_tracker' => true,
        ]);
    }
}
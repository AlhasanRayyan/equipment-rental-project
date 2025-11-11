<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\User;
use App\Models\EquipmentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        // نحاول نختار مستخدم عادي موجود أو نُنشئ واحد جديد
        $owner = User::where('role', 'user')->inRandomOrder()->first() ?? User::factory()->regularUser()->create();

        // نحاول نختار فئة عشوائية موجودة
        $category = EquipmentCategory::inRandomOrder()->first() ?? EquipmentCategory::factory()->create();

        return [
            'owner_id' => $owner->id,
            'category_id' => $category->id,
            'name' => $this->faker->words(2, true) . ' ' . $this->faker->randomElement(['حفارة', 'مولد', 'رافعة', 'منشار', 'مضخة']),
            'description' => $this->faker->sentence(10),
            'daily_rate' => $this->faker->randomFloat(2, 50, 300),
            'weekly_rate' => $this->faker->optional()->randomFloat(2, 300, 1000),
            'monthly_rate' => $this->faker->optional()->randomFloat(2, 800, 3000),
            'deposit_amount' => $this->faker->randomFloat(2, 100, 500),
            'location_latitude' => $this->faker->latitude(),
            'location_longitude' => $this->faker->longitude(),
            'location_address' => $this->faker->address(),
            'status' => $this->faker->randomElement(['available', 'rented', 'maintenance', 'unavailable']),
            'is_approved_by_admin' => $this->faker->boolean(80),
            'has_gps_tracker' => $this->faker->boolean(30),
            'average_rating' => $this->faker->randomFloat(2, 1, 5),
            'total_reviews' => $this->faker->numberBetween(0, 100),
            'last_maintenance_date' => $this->faker->optional()->dateTimeBetween('-2 years', 'now'),
            'maintenance_notes' => $this->faker->optional()->sentence(),
        ];
    }

    /** المعدات المتاحة */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'available']);
    }

    /** المعدات المؤجرة */
    public function rented(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'rented']);
    }

    /** المعدات الموافق عليها من الأدمن */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => ['is_approved_by_admin' => true]);
    }

    /** المعدات غير الموافق عليها */
    public function unapproved(): static
    {
        return $this->state(fn (array $attributes) => ['is_approved_by_admin' => false]);
    }

    /** المعدات التي تحتوي على GPS */
    public function hasGpsTracker(): static
    {
        return $this->state(fn (array $attributes) => ['has_gps_tracker' => true]);
    }
}

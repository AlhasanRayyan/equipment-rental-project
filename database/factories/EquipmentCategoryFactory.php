<?php

namespace Database\Factories;

use App\Models\EquipmentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EquipmentCategory>
 */
class EquipmentCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EquipmentCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_name' => $this->faker->words(2, true) . ' ' . $this->faker->randomElement(['Tools', 'Machinery', 'Vehicles', 'Devices', 'Equipment', 'Electronics', 'Supplies', 'Instruments']), // أضفت بعض الكلمات لتنوع أكبر
            'description' => $this->faker->paragraph(),
            'image_url' => $this->faker->imageUrl(640, 480, 'categories', true),
            'parent_id' => null, // افتراضياً، لا يوجد والد (فئة رئيسية)
            'is_active' => $this->faker->boolean(95),
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the category is a parent category (explicitly set parent_id to null).
     */
    public function parentCategory(): static // **جديد: state لإنشاء فئة رئيسية**
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => null,
        ]);
    }

    /**
     * Indicate that the category is a subcategory of a given parent.
     */
    public function subcategoryOf(EquipmentCategory $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
        ]);
    }
}
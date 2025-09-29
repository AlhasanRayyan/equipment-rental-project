<?php

namespace Database\Factories;

use App\Models\EquipmentImage;
use App\Models\Equipment; // تأكد من استيراد Equipment
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EquipmentImage>
 */
class EquipmentImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EquipmentImage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure there is at least one equipment item to attach images to
        $equipment = Equipment::factory()->create();

        return [
            'equipment_id' => $equipment->id,
            'image_url' => $this->faker->imageUrl(640, 480, 'equipment', true),
            'is_main' => false, // Default to not main; Seeder will handle setting one as main
            'display_order' => $this->faker->numberBetween(1, 10),
        ];
    }

    /**
     * Indicate that the image is the main image.
     */
    public function main(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_main' => true,
        ]);
    }

    /**
     * Configure the factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (EquipmentImage $image) {
            // If this is the first image created for an equipment, make it the main one.
            // Or if explicitly marked as main, ensure others are not.
            if ($image->is_main) {
                $image->equipment->images()->where('id', '!=', $image->id)->update(['is_main' => false]);
            } else {
                // If no main image exists, make the first one created for this equipment the main.
                if (!$image->equipment->images()->where('is_main', true)->exists()) {
                    $image->update(['is_main' => true]);
                }
            }
        });
    }
}
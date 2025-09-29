<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Equipment;
use App\Models\EquipmentImage;

class EquipmentImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there is equipment to attach images to
        if (Equipment::count() === 0) {
            $this->call(EquipmentSeeder::class); // Call EquipmentSeeder if no equipment exists
        }

        $equipments = Equipment::all();

        foreach ($equipments as $equipment) {
            // Create 3-5 images for each equipment
            $numImages = rand(3, 5);
            $images = EquipmentImage::factory($numImages)->for($equipment)->create();

            // Set one of the images as the main image (if not already set by factory configure method)
            if ($images->isNotEmpty()) {
                $images->first()->setAsMain(); // Using the setAsMain method from the model
            }
        }

        $this->command->info('Equipment Images seeded!');
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EquipmentCategory;

class EquipmentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some specific categories
        EquipmentCategory::create([
            'category_name' => 'Heavy Machinery',
            'description' => 'Large and powerful machines used for construction and other heavy tasks.',
            'image_url' => 'https://via.placeholder.com/640x480?text=Heavy+Machinery',
            'is_active' => true,
        ]);

        EquipmentCategory::create([
            'category_name' => 'Power Tools',
            'description' => 'Handheld or portable tools powered by electricity or compressed air.',
            'image_url' => 'https://via.placeholder.com/640x480?text=Power+Tools',
            'is_active' => true,
        ]);

        EquipmentCategory::create([
            'category_name' => 'Gardening Equipment',
            'description' => 'Tools and machines for maintaining gardens and landscapes.',
            'image_url' => 'https://via.placeholder.com/640x480?text=Gardening+Equipment',
            'is_active' => true,
        ]);

        // Create a few more generic categories using the factory
        EquipmentCategory::factory(7)->create();

        // Create an inactive category for testing purposes
        EquipmentCategory::factory()->inactive()->create([
            'category_name' => 'Deprecated Items',
            'description' => 'Category for items no longer in active use or testing.',
        ]);

        $this->command->info('Equipment Categories seeded!');
    }
}
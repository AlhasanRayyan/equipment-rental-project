<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Equipment;
use App\Models\User;
use App\Models\EquipmentCategory;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there are users (some will implicitly act as owners) and categories before creating equipment
        if (User::count() === 0) {
            $this->call(UserSeeder::class); // Make sure UserSeeder is called first
        }
        if (EquipmentCategory::count() === 0) {
            $this->call(EquipmentCategorySeeder::class); // Make sure EquipmentCategorySeeder is called first
        }

        // Create some approved and available equipment
        Equipment::factory(10)->approved()->available()->create();

        // Create some equipment that is currently rented
        Equipment::factory(5)->rented()->create();

        // Create some unapproved equipment (waiting for admin review)
        Equipment::factory(3)->unapproved()->create();

        // Create some general equipment with mixed states
        Equipment::factory(7)->create();

        $this->command->info('Equipment seeded!');
    }
}
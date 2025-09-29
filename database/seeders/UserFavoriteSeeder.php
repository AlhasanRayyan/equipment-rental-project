<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserFavorite;
use App\Models\User;
use App\Models\Equipment;

class UserFavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure dependencies exist: users (regular users for favorites), equipment
        if (User::where('role', 'user')->count() === 0) {
            $this->call(UserSeeder::class);
        }
        if (Equipment::count() === 0) {
            $this->call(EquipmentSeeder::class);
        }

        $users = User::where('role', 'user')->get(); // Only regular users favorite items
        $equipments = Equipment::all();

        // Create some favorites for each user
        foreach ($users as $user) {
            $numFavorites = rand(1, 5);
            // Ensure we don't try to get more unique items than available
            $availableEquipment = $equipments->whereNotIn('id', $user->favorites()->pluck('equipment_id'))->shuffle();
            if ($availableEquipment->isEmpty()) continue;

            $favoritedEquipmentIds = $availableEquipment->take($numFavorites)->pluck('id')->toArray();

            foreach ($favoritedEquipmentIds as $equipmentId) {
                UserFavorite::factory()->forUser($user)->forEquipment(Equipment::find($equipmentId))->create();
            }
        }

        $this->command->info('User Favorites seeded!');
    }
}
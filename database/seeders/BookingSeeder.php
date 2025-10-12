<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\User;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure dependencies exist before creating bookings
        if (User::where('role', 'user')->count() < 2) { // Need at least 2 regular users (one as owner, one as renter)
            $this->call(UserSeeder::class);
        }
        if (Equipment::count() === 0) {
            $this->call(EquipmentSeeder::class);
        }

        // Create some pending bookings
        Booking::factory(5)->pending()->create();

        // Create some confirmed bookings
        Booking::factory(7)->confirmed()->paid()->create();

        // Create some active bookings
        Booking::factory(8)->active()->paid()->create();

        // Create some completed bookings
        Booking::factory(10)->completed()->paid()->create();

        // Create some cancelled bookings
        Booking::factory(3)->cancelled()->create();

        $this->command->info('Bookings seeded!');
    }
}
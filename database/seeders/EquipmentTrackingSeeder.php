<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EquipmentTracking;
use App\Models\Equipment;
use Carbon\Carbon;
use Faker\Factory as Faker;

class EquipmentTrackingSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        if (Equipment::where('has_gps_tracker', true)->count() === 0) {
            if (\App\Models\User::count() === 0) {
                $this->call(UserSeeder::class);
            }

            if (\App\Models\EquipmentCategory::count() === 0) {
                $this->call(EquipmentCategorySeeder::class);
            }

            Equipment::factory(8)->hasGpsTracker()->create();
        }

        $equipmentsWithTracker = Equipment::where('has_gps_tracker', true)->get();

        $gazaLocations = [
            ['lat' => 31.5400, 'lng' => 34.5000], // شمال غزة
            ['lat' => 31.5200, 'lng' => 34.4700], // شمال القطاع
            ['lat' => 31.5017, 'lng' => 34.4668], // غزة
            ['lat' => 31.4700, 'lng' => 34.4300], // جنوب غزة
            ['lat' => 31.4200, 'lng' => 34.3800], // الوسطى
            ['lat' => 31.3800, 'lng' => 34.3400], // دير البلح
            ['lat' => 31.3500, 'lng' => 34.3100], // خانيونس
            ['lat' => 31.3000, 'lng' => 34.2500], // رفح
        ];

        foreach ($equipmentsWithTracker as $index => $equipment) {
            $basePoint = $gazaLocations[$index % count($gazaLocations)];

            $baseLat = $basePoint['lat'];
            $baseLng = $basePoint['lng'];

            $batteryLevel = $faker->randomFloat(2, 45, 100);

            $statuses = ['online', 'moving', 'idle', 'offline'];

            $currentDate = Carbon::now()->subDays(rand(1, 10));

            for ($i = 0; $i < rand(6, 12); $i++) {
                $status = $statuses[$i % count($statuses)];

                $lat = $baseLat + $faker->randomFloat(6, -0.015, 0.015);
                $lng = $baseLng + $faker->randomFloat(6, -0.015, 0.015);

                $lat = max(31.20, min(31.70, $lat));
                $lng = max(34.20, min(34.60, $lng));

                $speed = match ($status) {
                    'moving' => $faker->randomFloat(2, 10, 80),
                    'online' => $faker->randomFloat(2, 0, 10),
                    'idle' => 0,
                    'offline' => 0,
                    default => 0,
                };

                $batteryLevel -= $faker->randomFloat(2, 0.3, 2.5);
                if ($batteryLevel < 5) {
                    $batteryLevel = $faker->randomFloat(2, 20, 90);
                }

                EquipmentTracking::factory()
                    ->forEquipment($equipment)
                    ->create([
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'status' => $status,
                        'created_at' => $currentDate->copy()->addMinutes(rand(20, 120)),
                        'updated_at' => $currentDate->copy()->addMinutes(rand(20, 120)),
                    ]);
            }
        }

        $this->command->info('Equipment Tracking seeded with Gaza coordinates successfully!');
    }
}
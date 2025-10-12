<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EquipmentTracking;
use App\Models\Equipment;
use Carbon\Carbon;
use Faker\Factory as Faker; // تأكد من استيراد Faker هنا

class EquipmentTrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // تهيئة Faker للاستخدام داخل Seeder
        $faker = Faker::create();

        // التأكد من وجود المعدات، وخاصة تلك التي تحتوي على أجهزة تتبع GPS
        if (Equipment::where('has_gps_tracker', true)->count() === 0) {
            // إذا لم يكن هناك مستخدمون، قم باستدعاء UserSeeder
            if (\App\Models\User::count() === 0) {
                 $this->call(UserSeeder::class);
            }
            // إذا لم تكن هناك فئات للمعدات، قم باستدعاء EquipmentCategorySeeder
            if (\App\Models\EquipmentCategory::count() === 0) {
                 $this->call(EquipmentCategorySeeder::class);
            }
            // إنشاء 5 قطع معدات مزودة بأجهزة تتبع GPS إذا لم توجد
            Equipment::factory(5)->hasGpsTracker()->create();
        }

        // الحصول على جميع المعدات المزودة بأجهزة تتبع GPS
        $equipmentsWithTracker = Equipment::where('has_gps_tracker', true)->get();

        foreach ($equipmentsWithTracker as $equipment) {
            // توليد بيانات موقع ومستوى بطارية عشوائية أو استخدام الموجودة
            $latitude = $equipment->location_latitude ?? $faker->latitude();
            $longitude = $equipment->location_longitude ?? $faker->longitude();
            $batteryLevel = $faker->randomFloat(2, 50, 100);

            // إنشاء سجل تتبع أولي حديث بحالة "متصل"
            EquipmentTracking::factory()->forEquipment($equipment)->online()->create([
                'latitude' => $latitude,
                'longitude' => $longitude,
                'battery_level' => $batteryLevel,
                'created_at' => Carbon::now()->subMinutes(rand(1, 10)),
            ]);

            // إنشاء سلسلة من بيانات التتبع التاريخية لمحاكاة الحركة
            $numHistoricalRecords = rand(5, 15);
            $currentDate = Carbon::now()->subDays(rand(1, 20));
            $currentLat = $latitude;
            $currentLng = $longitude;

            for ($i = 0; $i < $numHistoricalRecords; $i++) {
                $status = $faker->randomElement(['online', 'moving', 'idle']);
                $speed = ($status === 'moving') ? $faker->randomFloat(2, 5, 60) : 0;

                // تغيير الإحداثيات قليلاً لمحاكاة الحركة
                $currentLat += $faker->randomFloat(6, -0.01, 0.01);
                $currentLng += $faker->randomFloat(6, -0.01, 0.01);

                // محاكاة استنزاف البطارية
                $batteryLevel -= $faker->randomFloat(2, 0.5, 2);
                if ($batteryLevel < 0) $batteryLevel = 0;

                // **تم التعديل هنا:** استخدام {$status}() لاستدعاء الدالة المرتبطة مباشرة في Factory
                EquipmentTracking::factory()->forEquipment($equipment)->{$status}()->create([
                    'latitude' => $currentLat,
                    'longitude' => $currentLng,
                    'speed' => $speed,
                    'battery_level' => $batteryLevel,
                    'created_at' => $currentDate->addMinutes(rand(10, 60)),
                ]);
            }

            // إضافة بعض سجلات "غير متصل" للتنوع
            if (rand(0,1)) {
                // **تم التعديل هنا أيضاً:** استدعاء الدوال مباشرة
                EquipmentTracking::factory()->forEquipment($equipment)->offline()->lowBattery()->create([
                    'created_at' => Carbon::now()->subDays(rand(2, 10))->subHours(rand(1,10)),
                ]);
            }
        }

        $this->command->info('Equipment Tracking seeded!');
    }
}
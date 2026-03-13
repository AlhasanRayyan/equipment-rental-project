<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() === 0) {
            $this->call(UserSeeder::class);
        }
        if (EquipmentCategory::count() === 0) {
            $this->call(EquipmentCategorySeeder::class);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Equipment::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $owner = User::where('role', 'user')->first() ?? User::factory()->create(['role' => 'user']);

        $categories = EquipmentCategory::whereNull('parent_id')->get();

        // نقاط غزة + position (من شمال غزة لرفح مرورًا بالوسطى)
        $gazaLocations = [
            ['position' => 'north_gaza',    'lat' => 31.5400, 'lng' => 34.5000],
            ['position' => 'north_gaza',    'lat' => 31.5200, 'lng' => 34.4700],
            ['position' => 'gaza_city',     'lat' => 31.5017, 'lng' => 34.4668],
            ['position' => 'gaza_city',     'lat' => 31.4700, 'lng' => 34.4300],
            ['position' => 'middle_area',   'lat' => 31.4200, 'lng' => 34.3800],
            ['position' => 'deir_al_balah', 'lat' => 31.3800, 'lng' => 34.3400],
            ['position' => 'khan_younis',   'lat' => 31.3500, 'lng' => 34.3100],
            ['position' => 'rafah',         'lat' => 31.3000, 'lng' => 34.2500],
        ];

        $equipmentData = [
            'معدات البناء والإنشاء'  => [
                ['name' => 'حفارة متوسطة', 'description' => 'حفارة قوية تستخدم في أعمال الحفر العميق وإنشاء الأساسات.'],
                ['name' => 'رافعة برجية', 'description' => 'رافعة ضخمة لرفع ونقل المواد الثقيلة في مواقع الإنشاء.'],
                ['name' => 'خلاطة إسمنت متنقلة', 'description' => 'خلاطة تستخدم لتحضير الخرسانة في مواقع العمل الصغيرة.'],
                ['name' => 'مدحلة طرق', 'description' => 'آلة لضغط وتسوية طبقات الأسفلت أو التربة في المشاريع.'],
            ],
            'معدات الطاقة والكهرباء' => [
                ['name' => 'مولد كهرباء ديزل 20 ك.ف.أ', 'description' => 'مولد طاقة قوي لتشغيل الأدوات والمعدات الثقيلة.'],
                ['name' => 'ألواح شمسية 450 واط', 'description' => 'ألواح طاقة عالية الكفاءة لتوليد الكهرباء من الشمس.'],
                ['name' => 'منظم جهد كهربائي', 'description' => 'جهاز يحافظ على استقرار التيار الكهربائي وحماية الأجهزة.'],
            ],
            'معدات المياه والسباكة'  => [
                ['name' => 'مضخة مياه غاطسة 2 بوصة', 'description' => 'مضخة فعالة لاستخراج المياه من الآبار أو الخزانات.'],
                ['name' => 'خراطيم ضغط عالي', 'description' => 'خراطيم متينة تتحمل ضغط المياه العالي لأغراض التنظيف أو الري.'],
            ],
            'معدات الزراعة والبيئة'  => [
                ['name' => 'جرار زراعي متوسط الحجم', 'description' => 'جرار متعدد الاستخدامات للأعمال الزراعية اليومية.'],
                ['name' => 'آلة رش مبيدات يدوية', 'description' => 'أداة لرش المبيدات الزراعية بشكل متساوٍ على النباتات.'],
                ['name' => 'منشار تقليم كهربائي', 'description' => 'منشار مخصص لتقليم الأشجار والأغصان بسهولة وسرعة.'],
                ['name' => 'مضخة ري بالوقود', 'description' => 'مضخة قوية تعمل بالبنزين لري الأراضي الزراعية.'],
                ['name' => 'آلة نثر سماد', 'description' => 'آلة تستخدم لتوزيع الأسمدة بشكل متساوٍ في الحقول.'],
            ],
            'معدات الحماية والسلامة' => [
                ['name' => 'خوذة أمان صناعية', 'description' => 'خوذة مصنوعة من مواد مقاومة للصدمات لحماية الرأس.'],
                ['name' => 'نظارات واقية شفافة', 'description' => 'نظارات تحمي العينين من الغبار والجزيئات المتطايرة.'],
                ['name' => 'قفازات مقاومة للحرارة', 'description' => 'قفازات توفر حماية عالية أثناء العمل في درجات حرارة مرتفعة.'],
            ],
            'معدات النقل والتحميل'   => [
                ['name' => 'رافعة شوكية كهربائية', 'description' => 'آلة مخصصة لرفع ونقل المواد داخل المستودعات والمصانع.'],
                ['name' => 'عربة نقل يدوية', 'description' => 'عربة صغيرة لنقل الأدوات والمعدات الخفيفة في الورش.'],
                ['name' => 'شاحنة نقل صغيرة', 'description' => 'شاحنة تستخدم لنقل البضائع أو المعدات إلى مواقع العمل.'],
                ['name' => 'سلم تحميل معدني', 'description' => 'سلم قوي يساعد على تحميل المعدات إلى العربات بسهولة.'],
            ],
        ];

        $locIndex = 0;

        foreach ($equipmentData as $categoryName => $equipments) {
            $category = $categories->firstWhere('category_name', $categoryName);
            if (! $category) continue;

            foreach ($equipments as $item) {
                $loc = $gazaLocations[$locIndex % count($gazaLocations)];
                $locIndex++;

                Equipment::create([
                    'owner_id'             => $owner->id,
                    'category_id'          => $category->id,
                    'name'                 => $item['name'],
                    'description'          => $item['description'],
                    'daily_rate'           => rand(50, 250),
                    'weekly_rate'          => rand(300, 1000),
                    'monthly_rate'         => rand(900, 3000),
                    'deposit_amount'       => rand(100, 500),
                    'location_latitude'    => $loc['lat'],
                    'location_longitude'   => $loc['lng'],
                    'location_address'     => 'قطاع غزة',
                    'position'             => $loc['position'],
                    'status'               => 'available',
                    'is_approved_by_admin' => true,
                    'has_gps_tracker'      => (bool) rand(0, 1), // لو بدك بعضهم GPS
                ]);
            }
        }

        $this->command->info(' تم إدخال المعدات الحقيقية مع مناطق غزة بنجاح!');
    }
}

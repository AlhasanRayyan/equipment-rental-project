<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EquipmentCategory;
use Faker\Factory as Faker; 


class EquipmentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create(); 

        // 1. إنشاء الفئات الرئيسية المحددة (بدون والد)
        $heavyMachinery = EquipmentCategory::firstOrCreate(
            ['category_name' => 'Heavy Machinery'],
            [
                'description' => 'Large and powerful machines for construction and earthmoving.',
                'image_url' => $faker->imageUrl(640, 480, 'heavy-machinery', true), // استخدام faker هنا
                'is_active' => true,
            ]
        );

        $powerTools = EquipmentCategory::firstOrCreate(
            ['category_name' => 'Power Tools'],
            [
                'description' => 'Handheld or portable tools powered by electricity or compressed air.',
                'image_url' => $faker->imageUrl(640, 480, 'power-tools', true),
                'is_active' => true,
            ]
        );

        $gardenEquipment = EquipmentCategory::firstOrCreate(
            ['category_name' => 'Gardening & Landscaping'],
            [
                'description' => 'Tools and machines for maintaining gardens and landscapes.',
                'image_url' => $faker->imageUrl(640, 480, 'gardening', true),
                'is_active' => true,
            ]
        );

        $vehicles = EquipmentCategory::firstOrCreate(
            ['category_name' => 'Vehicles & Transport'],
            [
                'description' => 'Vehicles for transport and heavy lifting.',
                'image_url' => $faker->imageUrl(640, 480, 'vehicles', true),
                'is_active' => true,
            ]
        );
         $electronics = EquipmentCategory::firstOrCreate(
            ['category_name' => 'Electronics & Gadgets'],
            [
                'description' => 'Various electronic devices and gadgets for rent.',
                'image_url' => $faker->imageUrl(640, 480, 'electronics', true),
                'is_active' => true,
            ]
        );

        // 2. إنشاء فئات فرعية مرتبطة بالفئات الرئيسية
        EquipmentCategory::firstOrCreate(
            ['category_name' => 'Excavators', 'parent_id' => $heavyMachinery->id],
            ['description' => 'Hydraulic excavators for digging.', 'image_url' => $faker->imageUrl(), 'is_active' => true]
        );
        EquipmentCategory::firstOrCreate(
            ['category_name' => 'Loaders', 'parent_id' => $heavyMachinery->id],
            ['description' => 'Wheel loaders for moving materials.', 'image_url' => $faker->imageUrl(), 'is_active' => true]
        );
        EquipmentCategory::firstOrCreate(
            ['category_name' => 'Drills', 'parent_id' => $powerTools->id],
            ['description' => 'Electric and cordless drills.', 'image_url' => $faker->imageUrl(), 'is_active' => true]
        );
        EquipmentCategory::firstOrCreate(
            ['category_name' => 'Saws', 'parent_id' => $powerTools->id],
            ['description' => 'Circular saws, jigsaws, and chainsaws.', 'image_url' => $faker->imageUrl(), 'is_active' => true]
        );
        EquipmentCategory::firstOrCreate(
            ['category_name' => 'Lawn Mowers', 'parent_id' => $gardenEquipment->id],
            ['description' => 'Equipment for maintaining lawns.', 'image_url' => $faker->imageUrl(), 'is_active' => true]
        );
        EquipmentCategory::firstOrCreate(
            ['category_name' => 'Generators', 'parent_id' => $electronics->id],
            ['description' => 'Portable power generators.', 'image_url' => $faker->imageUrl(), 'is_active' => true]
        );


        // 3. إنشاء عدد قليل من الفئات الرئيسية العشوائية الأخرى
        EquipmentCategory::factory(3)->parentCategory()->create(); // **تم التعديل هنا: استخدام parentCategory()**

        // 4. إنشاء عدد قليل من الفئات الفرعية العشوائية
        // نجمع كل الفئات الموجودة ونختار منها كوالد عشوائي
        $allCategories = EquipmentCategory::all();
        if ($allCategories->count() > 0) {
            foreach (range(1, 5) as $i) {
                $parent = $allCategories->random();
                // نضمن أن الفئة الفرعية لا تكون والداً لنفسها
                if ($parent->id !== null) { // تأكد من أن الوالد ليس null
                    EquipmentCategory::factory()->subcategoryOf($parent)->create();
                }
            }
        }

        $this->command->info('Equipment Categories (with parents) seeded!');
    }
}
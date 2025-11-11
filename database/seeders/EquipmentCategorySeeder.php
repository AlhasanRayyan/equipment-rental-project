<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EquipmentCategory;
use App\Models\Equipment;
use Illuminate\Support\Facades\DB;
use Database\Factories\EquipmentCategoryFactory;

class EquipmentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // عشان احذف البيانات القديمة الوهمية
        Equipment::truncate();
        EquipmentCategory::truncate();

        //  من هنة بضيف البيانات الحقيقية
        $factory = new EquipmentCategoryFactory();
        $data    = $factory->definition();

        foreach ($data['mainCategories'] as $main => $desc) {
            $parent = EquipmentCategory::firstOrCreate(
                ['category_name' => $main],
                [
                    'description' => $desc,
                    'is_active'   => true,
                    'image_url'   => null,
                ]
            );

            if (isset($data['subCategories'][$main])) {
                foreach ($data['subCategories'][$main] as $sub) {
                    EquipmentCategory::firstOrCreate(
                        [
                            'category_name' => $sub['name'],
                            'parent_id'     => $parent->id,
                        ],
                        [
                            'description' => $sub['description'],
                            'is_active'   => true,
                            'image_url'   => null,
                        ]
                    );
                }
            }
        }

        $this->command->info('✅ تم إدخال جميع الفئات الرئيسية والفرعية بنجاح.');
    }
}

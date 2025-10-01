<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminSetting;
use App\Models\User; // لربط updated_by

class AdminSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التأكد من وجود مستخدم مسؤول لربط 'updated_by'
        if (User::where('role', 'admin')->count() === 0) {
            User::factory()->admin()->create();
        }

        // الحصول على مستخدم مسؤول للربط
        $adminUser = User::where('role', 'admin')->first();
        $adminUserId = $adminUser ? $adminUser->id : null;

        // إنشاء إعدادات النظام الأساسية إذا لم تكن موجودة بالفعل
        AdminSetting::firstOrCreate(
            ['setting_key' => 'tax_rate_percent'],
            [
                'setting_value' => '15.00',
                'description' => 'Global tax rate applied to rental costs (%).',
                'updated_by' => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'contact_email'],
            [
                'setting_value' => 'support@equipmentrental.com',
                'description' => 'Main contact email for customer support.',
                'updated_by' => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'minimum_rental_days'],
            [
                'setting_value' => '1',
                'description' => 'Minimum number of days for an equipment rental.',
                'updated_by' => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'maintenance_mode'],
            [
                'setting_value' => 'false',
                'description' => 'Set to true to put the application in maintenance mode.',
                'updated_by' => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'terms_and_conditions_url'],
            [
                'setting_value' => 'https://www.equipmentrental.com/terms',
                'description' => 'URL to the terms and conditions page.',
                'updated_by' => $adminUserId,
            ]
        );

        // إنشاء بعض الإعدادات العشوائية الإضافية باستخدام Factory
        // نتأكد من أنها لا تتضارب مع الإعدادات الأساسية
        AdminSetting::factory(5)->create();

        $this->command->info('Admin Settings seeded!');
    }
}
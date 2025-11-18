<?php

namespace App\Http\Controllers\Category;

use App\Models\Equipment;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use App\Models\EquipmentCategory;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
        public function index()
    {
        // جلب جميع الفئات النشطة
        $categories = EquipmentCategory::where('is_active', true)->orderBy('category_name', 'asc')->get();

        // بيانات الفوتر من إعدادات النظام
        // $contactPhone = AdminSetting::where('setting_key', 'contact_phone')->first()->setting_value ?? '+970 59 723 4892';
        // $officeHours = AdminSetting::where('setting_key', 'office_hours')->first()->setting_value ?? 'السبت - الخميس ( 8ص - 6م)';
        // $contactEmail = AdminSetting::where('setting_key', 'contact_email')->first()->setting_value ?? 'rentals@my-domain.net';
        // $siteDescription = AdminSetting::where('setting_key', 'site_description')->first()->setting_value ?? 'منصة تتيح للمستخدمين خدمات من تأجير واستئجار معدات البناء.';

        return view('frontend.categories', compact('categories',
        // 'contactPhone',
        // 'officeHours', 'contactEmail', 'siteDescription'
    ));
    }

}

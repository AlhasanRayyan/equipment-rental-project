<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EquipmentCategory;
use App\Models\AdminSetting;
use App\Models\User;
use App\Models\Equipment;

class HomeController extends Controller
{
    /**
     * عرض الصفحة الرئيسية الديناميكية.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 1. بيانات قسم البحث (Find Section)
        $equipmentCategories = EquipmentCategory::where('is_active', true)->get();
        $locations = ['غزة', 'خان يونس', 'الوسطى', 'الشمال', 'رفح'];

        // 2. بيانات قسم فئات الآلات (Category Section)
        $featuredCategories = EquipmentCategory::parents()->active()->inRandomOrder()->take(6)->get();

        // 3. بيانات تذييل الصفحة (Footer) من إعدادات النظام
        $contactPhone = AdminSetting::where('setting_key', 'contact_phone')->first()->setting_value ?? '+970 59 723 4892'; // افتراض رقم هاتف جديد
        $officeHours = AdminSetting::where('setting_key', 'office_hours')->first()->setting_value ?? 'السبت - الخميس ( 8ص - 6م)'; // افتراض ساعات عمل جديدة
        $contactEmail = AdminSetting::where('setting_key', 'contact_email')->first()->setting_value ?? 'rentals@my-domain.net';
        $siteDescription = AdminSetting::where('setting_key', 'site_description')->first()->setting_value ?? 'منصة تتيح للمستخدمين خدمات من تأجير واستئجار معدات البناء بجميع أنواعها وبأسعار مناسبة'; // افتراض وصف للموقع

        return view('home', compact(
            'equipmentCategories',
            'locations',
            'featuredCategories',
            'contactPhone',
            'officeHours',
            'contactEmail',
            'siteDescription'
        ));
    }

    // public function categories()
    // {
    //     $parentCategories = EquipmentCategory::parents()->active()->with('children')->paginate(12);
    //     return view('categories', compact('parentCategories'));
    // }

    // public function equipments(Request $request)
    // {
    //     $query = $request->input('query');
    //     $category = $request->input('category');
    //     $location = $request->input('location');

    //     $equipments = Equipment::query()
    //         ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))
    //         ->when($category, fn($q) => $q->where('category_id', $category))
    //         ->when($location, fn($q) => $q->where('location_address', 'like', "%{$location}%")) // أو باستخدام Lat/Lng
    //         ->where('is_approved_by_admin', true) // عرض المعدات المعتمدة فقط
    //         ->where('status', 'available') // عرض المعدات المتاحة فقط
    //         ->with('category', 'images')
    //         ->paginate(12);

    //     $equipmentCategories = EquipmentCategory::where('is_active', true)->get();
    //     $locations = ['غزة', 'خان يونس', 'الوسطى', 'الشمال', 'رفح']; // نفس القائمة من index

    //     return view('equipments', compact('equipments', 'equipmentCategories', 'locations', 'query', 'category', 'location'));
    // }

    public function about()
    {
        $siteDescription = AdminSetting::where('setting_key', 'site_description')->first()->setting_value ?? 'منصة تأجير معدات البناء ...';
        return view('about', compact('siteDescription'));
    }

    public function contact()
    {
        $contactEmail = AdminSetting::where('setting_key', 'contact_email')->first()->setting_value ?? 'support@equipmentrental.com';
        $contactPhone = AdminSetting::where('setting_key', 'contact_phone')->first()->setting_value ?? '+970 59 723 4892';
        return view('contact', compact('contactEmail', 'contactPhone'));
    }
}

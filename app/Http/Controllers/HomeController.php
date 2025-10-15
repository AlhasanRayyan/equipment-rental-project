<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EquipmentCategory;
use App\Models\AdminSetting;
use App\Models\User;
use App\Models\Equipment;

class HomeController extends Controller
{
    public function index()
    {
        $equipmentCategories = EquipmentCategory::where('is_active', true)->with('children')->get(); // تحميل children هنا
        $locations = ['غزة', 'خان يونس', 'الوسطى', 'الشمال', 'رفح'];

        $featuredCategories = EquipmentCategory::parents()->active()->inRandomOrder()->take(6)->get();

        $contactPhone = AdminSetting::where('setting_key', 'contact_phone')->first()->setting_value ?? '+970 59 723 4892';
        $officeHours = AdminSetting::where('setting_key', 'office_hours')->first()->setting_value ?? 'السبت - الخميس ( 8ص - 6م)';
        $contactEmail = AdminSetting::where('setting_key', 'contact_email')->first()->setting_value ?? 'rentals@my-domain.net';
        $siteDescription = AdminSetting::where('setting_key', 'site_description')->first()->setting_value ?? 'منصة تتيح للمستخدمين خدمات من تأجير واستئجار معدات بجميع أنواعها وبأسعار مناسبة';

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

    public function categories(Request $request)
    {
        $parentId = $request->input('parent_id');

        if ($parentId) {
            $parentCategory = EquipmentCategory::find($parentId);
            if (!$parentCategory) {
                abort(404);
            }
            $categories = $parentCategory->children()->active()->with('children')->paginate(12);
            $currentParent = $parentCategory;
        } else {
            $categories = EquipmentCategory::parents()->active()->with('children')->paginate(12);
            $currentParent = null;
        }

        return view('categories', compact('categories', 'currentParent'));
    }

    public function equipments(Request $request)
    {
        $query = $request->input('query');
        $categoryId = $request->input('category');
        $location = $request->input('location');
        $minDailyRate = $request->input('min_daily_rate');
        $maxDailyRate = $request->input('max_daily_rate');

        $equipments = Equipment::query()
            ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->when($categoryId, function ($q) use ($categoryId) {
                $selectedCategory = EquipmentCategory::find($categoryId);
                if ($selectedCategory) {
                    $categoryIds = [$selectedCategory->id];
                    $this->getAllChildrenIds($selectedCategory, $categoryIds);
                    $q->whereIn('category_id', array_unique($categoryIds)); // array_unique لتحسين الأداء وتجنب التكرار
                }
            })
            ->when($location, fn($q) => $q->where('location_address', 'like', "%{$location}%"))
            ->when($minDailyRate, fn($q) => $q->where('daily_rate', '>=', $minDailyRate))
            ->when($maxDailyRate, fn($q) => $q->where('daily_rate', '<=', $maxDailyRate)) // **تصحيح الخطأ الإملائي هنا**
            ->where('is_approved_by_admin', true)
            ->where('status', 'available')
            ->with('owner', 'category', 'images')
            ->paginate(12);

        // هذه المتغيرات ضرورية لفلتر البحث في صفحة المعدات
        $equipmentCategories = EquipmentCategory::where('is_active', true)->with('children')->get();
        $locations = ['غزة', 'خان يونس', 'الوسطى', 'الشمال', 'رفح'];

        // **تصحيح الـ View الراجع**: الآن يشير إلى 'equipments' بدلاً من 'home'
        // ويمرر جميع المتغيرات اللازمة لتعبئة الفلاتر وعرض النتائج
        return view('frontend.equipments', compact(
            'equipments',
            'equipmentCategories', // تم تغيير اسم المتغير ليتوافق مع الـ View
            'locations',
            'query',
            'categoryId',
            'location',
            'minDailyRate',
            'maxDailyRate'
        ));
    }


    private function getAllChildrenIds(EquipmentCategory $category, array &$ids): void
    {
        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $this->getAllChildrenIds($child, $ids);
        }
    }
}

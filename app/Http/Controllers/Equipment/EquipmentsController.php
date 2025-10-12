<?php
namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;

class EquipmentsController extends Controller
{
    public function index(Request $request)
    {
        $query    = $request->input('query');
        $category = $request->input('category');

        $equipments = Equipment::query()
            ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->when($category, fn($q) => $q->where('category_id', $category))
            ->where('is_approved_by_admin', true)
            ->where('status', 'available')
            ->with('category', 'owner')
            ->paginate(9);

        $categories = EquipmentCategory::where('is_active', true)->get();

        $contactPhone    = AdminSetting::where('setting_key', 'contact_phone')->first()->setting_value ?? '+970 59 723 4892';
        $officeHours     = AdminSetting::where('setting_key', 'office_hours')->first()->setting_value ?? 'السبت - الخميس ( 8ص - 6م)';
        $contactEmail    = AdminSetting::where('setting_key', 'contact_email')->first()->setting_value ?? 'rentals@my-domain.net';
        $siteDescription = AdminSetting::where('setting_key', 'site_description')->first()->setting_value ?? 'منصة تتيح للمستخدمين خدمات من تأجير واستئجار معدات البناء.';

        return view('frontend.equipments', compact('equipments', 'categories', 'contactPhone', 'officeHours', 'contactEmail', 'siteDescription'));
    }

}

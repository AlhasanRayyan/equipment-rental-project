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


    public function create()
    {
        $categories = EquipmentCategory::active()->get(); // أو جميع الفئات
        return view('frontend.equipments.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:equipment_categories,id',
            'description' => 'nullable|string',
            'daily_rate' => 'nullable|numeric',
            'weekly_rate' => 'nullable|numeric',
            'monthly_rate' => 'nullable|numeric',
            'deposit_amount' => 'nullable|numeric',
            'location_address' => 'required|string|max:255',
            'has_gps_tracker' => 'boolean',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $equipment = Equipment::create($data + ['owner_id' => auth()->id(), 'status' => 'available']);

        // حفظ الصور
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('equipment', 'public');
                $equipment->images()->create(['image_url' => $path]);
            }
        }

        return redirect()->route('equipments.create')->with('success', 'تمت إضافة المعدة بنجاح');
    }

    public function edit($id)
    {
        $equipment = Equipment::with('images')->findOrFail($id);
        $categories = EquipmentCategory::all();
        return view('frontend.equipments.edit', compact('equipment', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:equipment_categories,id',
            'location_address' => 'required|string|max:255',
            'daily_rate' => 'nullable|numeric',
            'weekly_rate' => 'nullable|numeric',
            'monthly_rate' => 'nullable|numeric',
            'deposit_amount' => 'nullable|numeric',
            'description' => 'nullable|string',
            'has_gps_tracker' => 'boolean',
            'images.*' => 'image|max:2048',
        ]);

        $equipment->update($validated);

        // ✅ إذا رفع صور جديدة
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('equipments', 'public');
                $equipment->images()->create(['image_url' => $path]);
            }
        }

        return redirect()->route('home')->with('success', 'تم تحديث بيانات المعدة بنجاح!');
    }
}

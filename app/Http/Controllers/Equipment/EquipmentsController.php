<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipmentsController extends Controller
{
    public function index(Request $request)
    {
        $query    = $request->input('query');
        $category = $request->input('category');

        // القسم الأول — كل المعدات
        $equipments = Equipment::query()
            ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->when($category, fn($q) => $q->where('category_id', $category))
            ->where('is_approved_by_admin', true)
            ->where('status', 'available')
            ->with('category', 'owner')
            ->paginate(9);

        // القسم الثاني — بناءً على الاهتمامات
        $recommendedEquipments = collect();

        if (auth()->check()) {
            $interestIds = auth()->user()->interests->pluck('id');

            if ($interestIds->isNotEmpty()) {
                $recommendedEquipments = Equipment::query()
                    ->whereIn('category_id', $interestIds)
                    ->where('is_approved_by_admin', true)
                    ->where('status', 'available')
                    ->with('category', 'owner')
                    ->latest()
                    ->take(6)
                    ->get();
            }
        }

        $categories = EquipmentCategory::where('is_active', true)->get();

        return view('frontend.equipments', compact(
            'equipments',
            'categories',
            'recommendedEquipments',
        ));
    }

    public function create()
    {
        $categories = EquipmentCategory::active()->get(); // أو جميع الفئات
        $positions = Equipment::POSITION_LABELS; // الحصول على المناطق من موديل Equipment

        return view('frontend.equipments.create', compact('categories', 'positions')); // تمرير $positions إلى الواجهة
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'category_id'      => 'required|exists:equipment_categories,id',
            'description'      => 'nullable|string',
            'daily_rate'       => 'nullable|numeric',
            'weekly_rate'      => 'nullable|numeric',
            'monthly_rate'     => 'nullable|numeric',
            'deposit_amount'   => 'nullable|numeric',
            'location_address' => 'required|string|max:255',
            'position'         => ['required', 'string', Rule::in(array_keys(Equipment::POSITION_LABELS))], // إضافة التحقق لحقل position

            'has_gps_tracker'  => 'boolean',
            'images.*'         => 'image|mimes:jpg,jpeg,png|max:2048',
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
        $equipment  = Equipment::with('images')->findOrFail($id);
        $categories = EquipmentCategory::all();
        return view('frontend.equipments.edit', compact('equipment', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'category_id'      => 'required|exists:equipment_categories,id',
            'location_address' => 'required|string|max:255',
            'daily_rate'       => 'nullable|numeric',
            'weekly_rate'      => 'nullable|numeric',
            'monthly_rate'     => 'nullable|numeric',
            'deposit_amount'   => 'nullable|numeric',
            'description'      => 'nullable|string',
            'has_gps_tracker'  => 'boolean',
            'images.*'         => 'image|max:2048',
        ]);

        $equipment->update($validated);

        // إذا رفع صور جديدة
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('equipments', 'public');
                $equipment->images()->create(['image_url' => $path]);
            }
        }

        return redirect()->route('owner.equipments')->with('success', 'تم تحديث بيانات المعدة بنجاح!');
    }

    public function show($id)
    {
        $equipment = Equipment::with(['images', 'owner', 'category'])->findOrFail($id);

        $isFavorite = false;
        if (auth()->check()) {
            $isFavorite = \App\Models\UserFavorite::where('user_id', auth()->id())
                ->where('equipment_id', $id)
                ->exists();
        }

        return view('frontend.equipments.show', compact('equipment', 'isFavorite'));
    }
}

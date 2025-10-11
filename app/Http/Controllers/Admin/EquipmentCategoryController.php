<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EquipmentCategory; // تأكد من استيراد الـ Model
use App\Models\Equipment; // قد تحتاجها لعرض المعدات
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EquipmentCategoryController extends Controller
{
    /**
     * عرض قائمة بجميع فئات المعدات.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = $request->input('query');

        $categories = EquipmentCategory::query()
            ->when($query, function ($q, $query) {
                $q->where('category_name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->withCount('equipment')
            ->with('parent') // **جديد: تحميل علاقة الوالد**
            ->orderBy('parent_id', 'asc') // **جديد: ترتيب حسب الوالد**
            ->orderBy('category_name', 'asc')
            ->paginate(10);

        $parentCategories = EquipmentCategory::parents()->active()->get(); // **جديد: لجلب الفئات الرئيسية لاختيارها كوالد**

        return view('dashboard.categories.index', compact('categories', 'query', 'parentCategories'));
    }

    /**
     * تخزين فئة جديدة.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => ['required', 'string', 'max:255', 'unique:equipment_categories'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'parent_id' => ['nullable', 'exists:equipment_categories,id'], // **جديد: التحقق من وجود parent_id**
            'is_active' => ['boolean'],
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('category_images', 'public');
            $imageUrl = $imagePath;
        }

        EquipmentCategory::create([
            'category_name' => $request->category_name,
            'description' => $request->description,
            'image_url' => $imageUrl,
            'parent_id' => $request->parent_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'تم إضافة الفئة بنجاح.');
    }

    /**
     * تحديث معلومات فئة موجودة.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EquipmentCategory  $equipmentCategory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, EquipmentCategory $equipmentCategory)
    {
        $request->validate([
            'category_name' => ['required', 'string', 'max:255', Rule::unique('equipment_categories')->ignore($equipmentCategory->id)],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'parent_id' => ['nullable', 'exists:equipment_categories,id', Rule::notIn([$equipmentCategory->id])], // **جديد: parent_id يجب ألا تكون نفس id الفئة الحالية**
            'is_active' => ['boolean'],
        ]);

        $imageUrl = $equipmentCategory->image_url;

        if ($request->hasFile('image')) {
            if ($equipmentCategory->image_url && Storage::disk('public')->exists($equipmentCategory->image_url)) {
                Storage::disk('public')->delete($equipmentCategory->image_url);
            }
            $imagePath = $request->file('image')->store('category_images', 'public');
            $imageUrl = $imagePath;
        }

        $equipmentCategory->update([
            'category_name' => $request->category_name,
            'description' => $request->description,
            'image_url' => $imageUrl,
            'parent_id' => $request->parent_id, // **جديد: تحديث parent_id**
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'تم تحديث الفئة بنجاح.');
    }

    /**
     * حذف فئة.
     *
     * @param  \App\Models\EquipmentCategory  $equipmentCategory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(EquipmentCategory $equipmentCategory)
    {
        // تحقق: لا تسمح بحذف فئة إذا كانت مرتبطة بمعدات أو بفئات فرعية
        if ($equipmentCategory->equipment()->exists()) {
            return redirect()->route('admin.categories.index')->with('error', 'لا يمكن حذف الفئة لأنها مرتبطة بمعدات موجودة.');
        }
        if ($equipmentCategory->children()->exists()) { // **جديد: منع حذف الفئة إذا كان لها أبناء**
            return redirect()->route('admin.categories.index')->with('error', 'لا يمكن حذف الفئة لأنها تحتوي على فئات فرعية.');
        }

        $equipmentCategory->delete();

        return redirect()->route('admin.categories.index')->with('success', 'تم حذف الفئة بنجاح.');
    }

    /**
     * عرض جميع المعدات المرتبطة بفئة معينة.
     *
     * @param  \App\Models\EquipmentCategory  $equipmentCategory
     * @return \Illuminate\View\View
     */
    public function showEquipment(EquipmentCategory $equipmentCategory)
    {
        $equipment = $equipmentCategory->equipment()->with('owner', 'category', 'images')->paginate(10);

        return view('dashboard.categories.show-equipment', compact('equipmentCategory', 'equipment'));
    }
}

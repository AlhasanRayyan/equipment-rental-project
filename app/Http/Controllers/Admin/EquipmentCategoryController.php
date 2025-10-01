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
            ->withCount('equipment') // حساب عدد المعدات لكل فئة
            ->orderBy('category_name', 'asc')
            ->paginate(10);

        return view('dashboard.categories.index', compact('categories', 'query'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => ['required', 'string', 'max:255', 'unique:equipment_categories'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'], // **تعديل التحقق لملف صورة**
            'is_active' => ['boolean'],
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('category_images', 'public'); // تخزين الصورة في مجلد 'category_images' داخل 'storage/app/public'
            $imageUrl = $imagePath; // المسار النسبي الذي سيتم حفظه في قاعدة البيانات
        }

        EquipmentCategory::create([
            'category_name' => $request->category_name,
            'description' => $request->description,
            'image_url' => $imageUrl, // **حفظ المسار النسبي**
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'تم إضافة الفئة بنجاح.');
    }

    public function update(Request $request, EquipmentCategory $equipmentCategory)
    {
        $request->validate([
            'category_name' => ['required', 'string', 'max:255', Rule::unique('equipment_categories')->ignore($equipmentCategory->id)],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'], // **تعديل التحقق لملف صورة**
            'is_active' => ['boolean'],
        ]);

        $imageUrl = $equipmentCategory->image_url; // احتفظ بالصورة القديمة افتراضياً

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($equipmentCategory->image_url && Storage::disk('public')->exists($equipmentCategory->image_url)) {
                Storage::disk('public')->delete($equipmentCategory->image_url);
            }
            $imagePath = $request->file('image')->store('category_images', 'public');
            $imageUrl = $imagePath;
        }

        $equipmentCategory->update([
            'category_name' => $request->category_name,
            'description' => $request->description,
            'image_url' => $imageUrl, // **تحديث المسار النسبي**
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
        // تحقق: لا تسمح بحذف فئة إذا كانت مرتبطة بمعدات (onDelete('restrict') في migration)
        if ($equipmentCategory->equipment()->exists()) {
            return redirect()->route('admin.categories.index')->with('error', 'لا يمكن حذف الفئة لأنها مرتبطة بمعدات موجودة.');
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

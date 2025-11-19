<?php
namespace App\Http\Controllers\Admin;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\EquipmentCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

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
            ->with('parent')
            ->orderBy('parent_id', 'asc')
            ->orderBy('category_name', 'asc')
            ->paginate(10);

        $parentCategories = EquipmentCategory::parents()->active()->get();

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
            'description'   => ['nullable', 'string'],
            'image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'parent_id'     => ['nullable', 'exists:equipment_categories,id'],
            'is_active'     => ['boolean'],
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('category_images', 'public');
            $imageUrl  = $imagePath;
        }

        EquipmentCategory::create([
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'image_url'     => $imageUrl,
            'parent_id'     => $request->parent_id,
            'is_active'     => $request->has('is_active'),
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
            'description'   => ['nullable', 'string'],
            'image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'parent_id'     => ['nullable', 'exists:equipment_categories,id', Rule::notIn([$equipmentCategory->id])], // **جديد: parent_id يجب ألا تكون نفس id الفئة الحالية**
            'is_active'     => ['boolean'],
        ]);

        $imageUrl = $equipmentCategory->image_url;

        if ($request->hasFile('image')) {
            if ($equipmentCategory->image_url && Storage::disk('public')->exists($equipmentCategory->image_url)) {
                Storage::disk('public')->delete($equipmentCategory->image_url);
            }
            $imagePath = $request->file('image')->store('category_images', 'public');
            $imageUrl  = $imagePath;
        }

        $equipmentCategory->update([
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'image_url'     => $imageUrl,
            'parent_id'     => $request->parent_id,
            'is_active'     => $request->has('is_active'),
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
        if ($equipmentCategory->equipment()->exists()) {
            return redirect()->route('admin.categories.index')->with('error', 'لا يمكن حذف الفئة لأنها مرتبطة بمعدات موجودة.');
        }
        if ($equipmentCategory->children()->exists()) {
            return redirect()->route('admin.categories.index')->with('error', 'لا يمكن حذف الفئة لأنها تحتوي على فئات فرعية.');
        }

        $equipmentCategory->delete();

        return redirect()->route('admin.categories.index')->with('success', 'تم حذف الفئة بنجاح.');
    }
    public function trash(Request $request)
    {
        $query = $request->input('query');

        $categories = EquipmentCategory::onlyTrashed()
            ->when($query, function ($q, $query) {
                $q->where('category_name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('dashboard.categories.trash', compact('categories', 'query'));
    }

    public function restore($id)
    {
        $category = EquipmentCategory::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()->route('admin.categories.trash')->with('success', 'تم استعادة الفئة بنجاح.');
    }

    public function restoreAll()
    {
        EquipmentCategory::onlyTrashed()->restore();

        return redirect()->route('admin.categories.trash')->with('success', 'تم استعادة جميع الفئات المحذوفة.');
    }

    public function forceDelete($id)
    {
        $category = EquipmentCategory::onlyTrashed()->findOrFail($id);

        // ممنوع حذف نهائي إذا إلها أطفال أو معدات
        if ($category->children()->exists() || $category->equipment()->exists()) {
            return back()->with('error', 'لا يمكن حذف هذه الفئة نهائياً لأنها مرتبطة بفئات فرعية أو معدات.');
        }

        $category->forceDelete();

        return redirect()->route('admin.categories.trash')->with('success', 'تم حذف الفئة نهائياً.');
    }

    public function forceDeleteAll()
    {
        $categories   = EquipmentCategory::onlyTrashed()->get();
        $deletedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($categories as $category) {
                if ($category->children()->exists() || $category->equipment()->exists()) {
                    continue;
                }
                $category->forceDelete();
                $deletedCount++;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الحذف النهائي للفئات.');
        }

        return back()->with('success', "تم حذف {$deletedCount} فئة نهائياً (تم تخطي الفئات المرتبطة).");
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

    public function stats()
    {
        // نجيب كل الفئات مع عدد المعدات بكل فئة
        $categories = EquipmentCategory::withCount('equipment')
            ->with('parent')
            ->orderByDesc('equipment_count')
            ->get();

        $totalEquipments = $categories->sum('equipment_count');

        return view('dashboard.categories.stats', compact('categories', 'totalEquipments'));
    }

}

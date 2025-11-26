<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    /**
     * عرض قائمة بالمعدات بانتظار الموافقة أو جميع المعدات.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */

    public function index(Request $request)
    {
        $query        = $request->input('query');
        // لو نخليها بانتظار الموافقة زي ما كانت افضل عشان ينتبه عليها الادمن من اول ما يفتح لوحة التحكم
        $statusFilter = $request->input('status', 'all'); // افتراضياً: جميع المعدات
        $categoryId   = $request->input('category');      // لو جاي من لوحة التحكم لما يضغط على فئة

        $equipment = Equipment::with(['owner', 'category', 'images'])
            ->when($query, function ($q, $query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhereHas('owner', function ($sub) use ($query) {
                        $sub->where('first_name', 'like', "%{$query}%")
                            ->orWhere('last_name', 'like', "%{$query}%");
                    });
            })
            ->when($statusFilter !== 'all', function ($q) use ($statusFilter) {
                if ($statusFilter === 'pending') {
                    $q->where('is_approved_by_admin', false);
                } elseif ($statusFilter === 'approved') {
                    $q->where('is_approved_by_admin', true);
                }
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
        $categories = EquipmentCategory::all();

        return view('dashboard.equipment.index', compact(
            'equipment',
            'query',
            'statusFilter',
            'categoryId',
            'categories'
        ));
    }

    /**
     * الموافقة على معدة معينة.
     *
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Equipment $equipment)
    {
        $equipment->update(['is_approved_by_admin' => true]);

        return redirect()->route('admin.equipment.index')->with('success', 'تمت الموافقة على المعدة بنجاح.');
    }

    /**
     * رفض معدة معينة.
     *
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Equipment $equipment)
    {
        // يمكنك إضافة منطق هنا لإرسال إشعار للمالك أو طلب سبب الرفض
        $equipment->update([
            'is_approved_by_admin' => false,
            'status'               => 'unavailable',
        ]);

        return redirect()->route('admin.equipment.index')->with('success', 'تم رفض المعدة بنجاح.');
    }

    /**
     * حذف معدة.
     *
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Equipment $equipment)
    {
        // يجب أن تتحقق هنا من الصلاحيات لمنع أي أدمن من حذف أي معدة
        $equipment->delete();

        return redirect()->route('admin.equipment.index')->with('success', 'تم حذف المعدة بنجاح.');
    }

    public function trash(Request $request)
    {
        $query = $request->input('query');

        $equipment = Equipment::onlyTrashed()
            ->when($query, function ($q, $query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->with(['owner', 'category'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);
        // dd(view()->exists('dashboard.equipment.trash'));

        return view('dashboard.equipment.trash', compact('equipment', 'query'));
    }

    public function restore($id)
    {
        $equipment = Equipment::onlyTrashed()->findOrFail($id);
        $equipment->restore();

        return redirect()->route('admin.equipment.trash')->with('success', 'تم استعادة المعدة بنجاح.');
    }

    public function restoreAll()
    {
        Equipment::onlyTrashed()->restore();

        return redirect()->route('admin.equipment.trash')->with('success', 'تم استعادة جميع المعدات المحذوفة.');
    }

    public function forceDelete($id)
    {
        $equipment = Equipment::onlyTrashed()->findOrFail($id);

        // لو عليها حجوزات سابقة، إما تمنعي أو تعالجيها حسب منطقك
        if ($equipment->bookings()->exists()) {
            return back()->with('error', 'لا يمكن حذف هذه المعدة نهائياً لأنها مرتبطة بحجوزات.');
        }

        DB::beginTransaction();
        try {
            // حذف أي سجلات تابعة (صور، تتبع، ... لو عندك علاقات)
            if (method_exists($equipment, 'images')) {
                $equipment->images()->delete();
            }
            if (method_exists($equipment, 'trackingRecords')) {
                $equipment->trackingRecords()->delete();
            }

            $equipment->forceDelete();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الحذف النهائي للمعدة.');
        }

        return redirect()->route('admin.equipment.trash')->with('success', 'تم حذف المعدة نهائياً.');
    }

    public function forceDeleteAll()
    {
        $all          = Equipment::onlyTrashed()->get();
        $deletedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($all as $equipment) {
                if ($equipment->bookings()->exists()) {
                    continue; // لا نحذف نهائي لو عليه حجوزات
                }

                if (method_exists($equipment, 'images')) {
                    $equipment->images()->delete();
                }
                if (method_exists($equipment, 'trackingRecords')) {
                    $equipment->trackingRecords()->delete();
                }

                $equipment->forceDelete();
                $deletedCount++;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الحذف النهائي للمعدات.');
        }

        return back()->with('success', "تم حذف {$deletedCount} معدة نهائياً (تم تخطي المرتبطة بحجوزات).");
    }

    public function show(Equipment $equipment)
    {
                                                                              // يمكنك تحميل العلاقات اللازمة هنا إذا كنت تريد عرضها في صفحة التفاصيل
        $equipment->load(['owner', 'category', 'images', 'trackingRecords']); // مثال لتحميل العلاقات

        return view('dashboard.equipment.show', compact('equipment'));
    }

    public function stats()
    {
        // إجمالي المعدات
        $total = Equipment::count();

        // معدات موافَق عليها
        $approved = Equipment::where('is_approved_by_admin', true)->count();

        // معدات غير موافَق عليها (قيد المراجعة/مرفوضة حسب منطقك)
        $notApproved = Equipment::where('is_approved_by_admin', false)->count();

        // معدات متاحة
        $available = Equipment::where('status', 'available')->count();

        // معدات غير متاحة (مثلاً مرفوضة / معطّلة / مؤجَّرة)
        $unavailable = Equipment::where('status', 'unavailable')->count();

        // لو حابة عدد المعدات المقبولة والمتاحة فقط
        $approvedAndAvailable = Equipment::where('is_approved_by_admin', true)
            ->where('status', 'available')
            ->count();

        // إحصاءات حسب الفئة
        // يفترض إنه عندك علاقة equipments() في EquipmentCategory
        $categoriesStats = EquipmentCategory::withCount('equipment')->get();

        return view('dashboard.equipment.stats', compact(
            'total',
            'approved',
            'notApproved',
            'available',
            'unavailable',
            'approvedAndAvailable',
            'categoriesStats'
        ));
    }

}

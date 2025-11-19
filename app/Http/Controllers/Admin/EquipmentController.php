<?php
namespace App\Http\Controllers\Admin;

use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Models\EquipmentCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
        $statusFilter = $request->input('status', 'pending');

        $equipment = Equipment::query()
            ->when($query, function ($q, $query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->when($statusFilter === 'pending', function ($q) {
                $q->where('is_approved_by_admin', false);
            })
            ->when($statusFilter === 'approved', function ($q) {
                $q->where('is_approved_by_admin', true);
            })
            ->when($statusFilter === 'all', function ($q) {
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = EquipmentCategory::all();

        return view('dashboard.equipment.index', compact('equipment', 'query', 'statusFilter', 'categories'));
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

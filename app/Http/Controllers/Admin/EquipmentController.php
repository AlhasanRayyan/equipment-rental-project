<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment; 
use App\Models\EquipmentCategory; 

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
        $query = $request->input('query');
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
            'status' => 'unavailable', 
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

      public function show(Equipment $equipment)
    {
        // يمكنك تحميل العلاقات اللازمة هنا إذا كنت تريد عرضها في صفحة التفاصيل
        $equipment->load(['owner', 'category', 'images', 'trackingRecords']); // مثال لتحميل العلاقات

        return view('dashboard.equipment.show', compact('equipment'));
    }

}
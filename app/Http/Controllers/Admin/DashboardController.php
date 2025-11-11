<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Message;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. إحصائيات (عدد المستخدمين، المعدات، الحجوزات، الأرباح)
        $totalUsers     = User::count();
        $totalEquipment = Equipment::count();
        $totalBookings  = Booking::count();

        // إجمالي الأرباح: مجموع المدفوعات المكتملة ذات النوع 'initial_payment' أو 'final_payment'
        $totalRevenue = Payment::where('status', 'completed')
            ->whereIn('payment_type', ['initial_payment', 'final_payment'])
            ->sum('amount');

        $stats = [
            'total_users'     => $totalUsers,
            'total_equipment' => $totalEquipment,
            'total_bookings'  => $totalBookings,
            'total_revenue'   => number_format($totalRevenue, 2), // تنسيق الأرباح لكسرين عشريين
        ];

        // 2. المعدات بانتظار الموافقة (لمراجعة الإعلانات)
        $pendingEquipment = Equipment::where('is_approved_by_admin', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 3. آخر الشكاوى والاستفسارات
        // هنا نفترض أن الشكاوى والاستفسارات تُسجل في جدول الرسائل (Messages)
        $latestComplaints = Message::with('sender')
            ->where('is_resolved', false)
            ->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 4. بيانات الرسم البياني لتوزيع المعدات حسب الفئة
        // $equipmentCategoriesCount = EquipmentCategory::withCount('equipment')
        //     ->orderBy('equipment_count', 'desc')
        //     ->take(7)
        //     ->get();
        // 4. بيانات الرسم البياني لتوزيع المعدات حسب الفئة
        // $equipmentCategoriesCount = EquipmentCategory::whereNull('parent_id')
        //     ->withCount(['equipment as total_equipment_count' => function ($query) {
        //         $query->selectRaw('count(*)')
        //             ->orWhereIn('category_id', function ($subQuery) {
        //                 $subQuery->select('id')
        //                     ->from('equipment_categories')
        //                     ->whereColumn('parent_id', 'equipment_categories.id');
        //             });
        //     }])
        //     ->orderBy('total_equipment_count', 'desc')
        //     ->take(12)
        //     ->get();
        // 4. بيانات الرسم البياني لتوزيع المعدات حسب الفئة (الفئات الرئيسية فقط)
        $equipmentCategoriesCount = EquipmentCategory::whereNull('parent_id')
            ->with(['children.equipment', 'equipment'])
            ->get()
            ->map(function ($category) {
                // نحسب مجموع المعدات للفئة الرئيسية وجميع فئاتها الفرعية
                $totalEquipment = $category->equipment->count();
                foreach ($category->children as $child) {
                    $totalEquipment += $child->equipment->count();
                }

                return [
                    'category_name'   => $category->category_name,
                    'equipment_count' => $totalEquipment,
                ];
            })
            ->filter(fn($cat) => $cat['equipment_count'] > 0)
            ->sortByDesc('equipment_count')
            ->values();
        //  كل الفئات الرئيسية (حتى لو ما عندها معدات) لعرضها فوق الدائرة
        $allParentCategories = EquipmentCategory::whereNull('parent_id')
            ->orderBy('category_name')
            ->get();

        // 5. بيانات الرسم البياني للحجوزات الشهرية
        $monthlyBookings = Booking::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // تحويل الأشهر من تنسيق YYYY-MM إلى أسماء أشهر لغة عربية
        $monthlyBookings->map(function ($item) {
            $date             = Carbon::parse($item->month . '-01');
            $item->month_name = $date->translatedFormat('F Y'); // 'يناير 2023'
            return $item;
        });
// dd($equipmentCategoriesCount);

        return view('dashboard.index', compact(
            'stats',
            'pendingEquipment',
            'latestComplaints',
            'equipmentCategoriesCount',
            'monthlyBookings',
            'allParentCategories'
        ));
    }
}

<?php

namespace App\Providers;

use App\Models\AdminSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //  أهم سطر: أثناء artisan لا تشغّل أي Queries ولا View::share
        if ($this->app->runningInConsole()) {
            return;
        }

        Paginator::useBootstrapFive();

        // ✅ Composer للـ notifications (يوصل للـ navbar + صفحة الاشعارات)
        View::composer(['layouts.app', 'dashboard.read_notify'], function ($view) {

            $notifUI = [
                'booking_request'    => ['icon' => 'fas fa-calendar-plus',       'class' => 'text-primary',  'label' => 'طلب حجز'],
                'booking_confirmed'  => ['icon' => 'fas fa-check-circle',        'class' => 'text-success',  'label' => 'تأكيد حجز'],
                'booking_cancelled'  => ['icon' => 'fas fa-times-circle',        'class' => 'text-danger',   'label' => 'إلغاء حجز'],
                'new_message'        => ['icon' => 'fas fa-envelope',            'class' => 'text-info',     'label' => 'رسالة جديدة'],
                'equipment_approved' => ['icon' => 'fas fa-thumbs-up',           'class' => 'text-success',  'label' => 'موافقة معدة'],
                'equipment_rejected' => ['icon' => 'fas fa-thumbs-down',         'class' => 'text-danger',   'label' => 'رفض معدة'],
                'payment_received'   => ['icon' => 'fas fa-money-bill-wave',     'class' => 'text-success',  'label' => 'دفعة وصلت'],
                'payment_failed'     => ['icon' => 'fas fa-exclamation-triangle', 'class' => 'text-warning',  'label' => 'فشل دفع'],
                'refund_issued'      => ['icon' => 'fas fa-undo',                'class' => 'text-secondary', 'label' => 'استرداد'],
                'review_received'    => ['icon' => 'fas fa-star',                'class' => 'text-warning',  'label' => 'تقييم جديد'],
                'equipment_moved'    => ['icon' => 'fas fa-location-arrow',      'class' => 'text-danger',   'label' => 'تحذير حركة'],
                'system_alert'       => ['icon' => 'fas fa-bell',                'class' => 'text-dark',     'label' => 'تنبيه'],
                'low_battery'        => ['icon' => 'fas fa-battery-quarter',     'class' => 'text-warning',  'label' => 'بطارية منخفضة'],
                'equipment_offline'  => ['icon' => 'fas fa-wifi',                'class' => 'text-danger',   'label' => 'غير متصل'],
            ];

            $titles = [
                'booking_request'    => 'طلب حجز جديد',
                'booking_confirmed'  => 'تم تأكيد الحجز',
                'booking_cancelled'  => 'تم إلغاء الحجز',
                'new_message'        => 'رسالة جديدة',
                'equipment_approved' => 'تمت الموافقة على المعدة',
                'equipment_rejected' => 'تم رفض المعدة',
                'payment_received'   => 'تم استلام دفعة',
                'payment_failed'     => 'فشل الدفع',
                'refund_issued'      => 'تم إصدار استرداد',
                'review_received'    => 'تقييم جديد',
                'system_alert'       => 'تنبيه من النظام',
                'equipment_moved'    => 'تحركت المعدة من مكانها',
                'low_battery'        => 'بطارية منخفضة',
                'equipment_offline'  => 'الجهاز غير متصل',
            ];

            // لو مش عامل login
            if (!Auth::check()) {
                $view->with([
                    'unreadCount' => 0,
                    'latestNotifications' => collect(),
                    'notifUI' => $notifUI,
                    'titles' => $titles,
                ]);
                return;
            }

            $user = Auth::user();

            $view->with([
                'unreadCount' => $user->unreadNotifications()->count(),
                'latestNotifications' => $user->notifications()->latest()->take(5)->get(),
                'notifUI' => $notifUI,
                'titles' => $titles,
            ]);
        });
    }
}

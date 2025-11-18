<?php
namespace App\Providers;

use App\Models\AdminSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // المفاتيح اللي بدنا إياها من جدول admin_settings
        $keys = [
            'site_description',
            'contact_phone',
            'contact_hours', // نفس اسم المفتاح في الـ seeder
            'contact_email',
        ];

        // نجيبهم مرة واحدة على شكل مصفوفة [key => value]
        $settings = AdminSetting::whereIn('setting_key', $keys)
            ->pluck('setting_value', 'setting_key')
            ->toArray();

        // نشاركهم مع كل الـ views
        View::share([
            'siteDescription' => $settings['site_description'] ?? 'منصة تأجير معدات البناء.',
            'contactPhone'    => $settings['contact_phone'] ?? '',
            'officeHours'     => $settings['contact_hours'] ?? '',
            'contactEmail'    => $settings['contact_email'] ?? '',
        ]);
    }

    // public function boot(): void
    // {
    //     //
    //     Paginator::useBootstrapFive();
    //     // نجيب الإعدادات مرة واحدة
    //     $contactPhone = AdminSetting::where('setting_key', 'contact_phone')->value('setting_value') ?? '+970 59 723 4892';

    //     $officeHours = AdminSetting::where('setting_key', 'office_hours')->value('setting_value') ?? 'السبت - الخميس ( 8ص - 6م)';

    //     $contactEmail = AdminSetting::where('setting_key', 'contact_email')->value('setting_value') ?? 'rentals@my-domain.net';

    //     $siteDescription = AdminSetting::where('setting_key', 'site_description')->value('setting_value') ?? 'منصة تتيح للمستخدمين خدمات من تأجير واستئجار معدات بجميع أنواعها وبأسعار مناسبة';

    //     // نشارك إعدادات الفوتر مع كل الصفحات اللي تستخدم layouts.master
    //     View::composer('layouts.master', function ($view) {
    //         $keys = [
    //             'site_description',
    //             'contact_phone',
    //             'contact_hours',
    //             'contact_email',
    //         ];

    //         $settings = AdminSetting::whereIn('setting_key', $keys)
    //             ->pluck('setting_value', 'setting_key')
    //             ->toArray();

    //         $view->with([
    //             'siteDescription' => $settings['site_description'] ?? 'منصة تأجير معدات البناء.',
    //             'contactPhone'    => $settings['contact_phone'] ?? '',
    //             'officeHours'     => $settings['contact_hours'] ?? '',
    //             'contactEmail'    => $settings['contact_email'] ?? '',
    //         ]);
    //     });

    //     // // نشاركهم مع كل الـ views
    //     // View::share('contactPhone', $contactPhone);
    //     // View::share('officeHours', $officeHours);
    //     // View::share('contactEmail', $contactEmail);
    //     // View::share('siteDescription', $siteDescription);
    // }
}

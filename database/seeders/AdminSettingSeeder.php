<?php
namespace Database\Seeders;

use App\Models\AdminSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

// لربط updated_by

class AdminSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التأكد من وجود مستخدم مسؤول لربط 'updated_by'
        if (User::where('role', 'admin')->count() === 0) {
            User::factory()->admin()->create();
        }

        // الحصول على مستخدم مسؤول للربط
        $adminUser   = User::where('role', 'admin')->first();
        $adminUserId = $adminUser ? $adminUser->id : null;

        // إنشاء إعدادات النظام الأساسية إذا لم تكن موجودة بالفعل
        AdminSetting::firstOrCreate(
            ['setting_key' => 'tax_rate_percent'],
            [
                'setting_value' => '15.00',
                'description'   => 'Global tax rate applied to rental costs (%).',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'contact_email'],
            [
                'setting_value' => 'support@equipmentrental.com',
                'description'   => 'Main contact email for customer support.',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'minimum_rental_days'],
            [
                'setting_value' => '1',
                'description'   => 'Minimum number of days for an equipment rental.',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'maintenance_mode'],
            [
                'setting_value' => 'false',
                'description'   => 'Set to true to put the application in maintenance mode.',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'terms_and_conditions_url'],
            [
                'setting_value' => 'https://www.equipmentrental.com/terms',
                'description'   => 'URL to the terms and conditions page.',
                'updated_by'    => $adminUserId,
            ]
        );

        // إنشاء بعض الإعدادات العشوائية الإضافية باستخدام Factory
        // نتأكد من أنها لا تتضارب مع الإعدادات الأساسية
        // AdminSetting::factory(5)->create();

        // $this->command->info('Admin Settings seeded!');

        // ========== إعدادات واجهة الموقع (Home / About) ==========

        // سكشن الخطوات - عنوانين
        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_steps_subtitle'],
            [
                'setting_value' => 'نعدك بالعثور على المعدات المناسبة لك',
                'description'   => 'العنوان الصغير في سكشن الخطوات',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_steps_title'],
            [
                'setting_value' => 'احصل على إيجاراتك بخطوات سهلة',
                'description'   => 'العنوان الكبير في سكشن الخطوات',
                'updated_by'    => $adminUserId,
            ]
        );

        // سكشن الخطوات - عناوين المربعات الأربعة
        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_step1_title'],
            [
                'setting_value' => 'ابحث عن معداتك',
                'description'   => 'عنوان الخطوة الأولى',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_step2_title'],
            [
                'setting_value' => 'إختر معدتك',
                'description'   => 'عنوان الخطوة الثانية',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_step3_title'],
            [
                'setting_value' => 'حجز المعدات',
                'description'   => 'عنوان الخطوة الثالثة',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_step4_title'],
            [
                'setting_value' => 'استلم المعدة',
                'description'   => 'عنوان الخطوة الرابعة',
                'updated_by'    => $adminUserId,
            ]
        );

        // سكشن "لماذا تختار منصتنا" - العناوين
        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_why_subtitle'],
            [
                'setting_value' => 'لماذا تختار منصتنا',
                'description'   => 'العنوان الصغير في سكشن لماذا نحن',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_why_title'],
            [
                'setting_value' => 'معدات متنوعة و طرق إيجار موثوقة',
                'description'   => 'العنوان الكبير في سكشن لماذا نحن',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_why_text'],
            [
                'setting_value' => 'تهدف المنصة إلى تسهيل عملية التأجير، تعزيز الشفافية، ودعم إعادة الإعمار من خلال حلول رقمية حديثة تخدم قطاع البناء والمقاولات.',
                'description'   => 'الفقرة النصية أسفل العنوان في سكشن لماذا نحن',
                'updated_by'    => $adminUserId,
            ]
        );

        // سكشن "لماذا تختار منصتنا" - عناوين المربعات الأربعة
        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_why_box1_title'],
            [
                'setting_value' => 'جودة معدات عالية',
                'description'   => 'عنوان البوكس الأول في سكشن لماذا نحن',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_why_box2_title'],
            [
                'setting_value' => 'موثوقية وخدمة سريعة',
                'description'   => 'عنوان البوكس الثاني في سكشن لماذا نحن',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_why_box3_title'],
            [
                'setting_value' => 'أفضل الأسعار',
                'description'   => 'عنوان البوكس الثالث في سكشن لماذا نحن',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_why_box4_title'],
            [
                'setting_value' => 'الإيجار مع أمان كامل',
                'description'   => 'عنوان البوكس الرابع في سكشن لماذا نحن',
                'updated_by'    => $adminUserId,
            ]
        );

        // سكشن CTA (ابدأ الآن)
        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_cta_title'],
            [
                'setting_value' => 'هل أنت قلق بشأن وقوف المعدات في الخلاء ؟',
                'description'   => 'عنوان سكشن الدعوة لاتخاذ إجراء في الأسفل',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_cta_text'],
            [
                'setting_value' => 'ابدأ بإدراج معداتك معنا اليوم!',
                'description'   => 'النص أسفل العنوان في سكشن الدعوة لاتخاذ إجراء',
                'updated_by'    => $adminUserId,
            ]
        );
        // سكشن الخطوات - نصوص الـ Tooltip لكل خطوة
        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_step1_tooltip'],
            [
                'setting_value' => 'قم بالبحث عن المعدة التي تناسبك',
                'description'   => 'نص التلميح (tooltip) للخطوة الأولى في سكشن الخطوات',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_step2_tooltip'],
            [
                'setting_value' => 'إختر المعدة المناسبة لعملك',
                'description'   => 'نص التلميح (tooltip) للخطوة الثانية في سكشن الخطوات',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_step3_tooltip'],
            [
                'setting_value' => 'احجز المعدة واكمل باقي الإجراءات بسهولة',
                'description'   => 'نص التلميح (tooltip) للخطوة الثالثة في سكشن الخطوات',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_step4_tooltip'],
            [
                'setting_value' => 'استلم المعدة المطلوبة في الموعد المحدد',
                'description'   => 'نص التلميح (tooltip) للخطوة الرابعة في سكشن الخطوات',
                'updated_by'    => $adminUserId,
            ]
        );

        // سكشن الخطوات - أيقونات الخطوات
        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_step1_icon'],
            [
                'setting_value' => 'assets/home/img/icons/ico-step-1.png',
                'description'   => 'أيقونة الخطوة الأولى في سكشن الخطوات',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_step2_icon'],
            [
                'setting_value' => 'assets/home/img/icons/ico-step-2.png',
                'description'   => 'أيقونة الخطوة الثانية في سكشن الخطوات',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_step3_icon'],
            [
                'setting_value' => 'assets/home/img/icons/ico-step-3.png',
                'description'   => 'أيقونة الخطوة الثالثة في سكشن الخطوات',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_step4_icon'],
            [
                'setting_value' => 'assets/home/img/icons/ico-step-4.png',
                'description'   => 'أيقونة الخطوة الرابعة في سكشن الخطوات',
                'updated_by'    => $adminUserId,
            ]
        );

// سكشن "لماذا تختار منصتنا" - أيقونات البوكسات
        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_why_box1_icon'],
            [
                'setting_value' => 'assets/home/img/icons/ico-why-choose-1.svg',
                'description'   => 'أيقونة البوكس الأول في سكشن لماذا تختار منصتنا',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_why_box2_icon'],
            [
                'setting_value' => 'assets/home/img/icons/ico-why-choose-2.svg',
                'description'   => 'أيقونة البوكس الثاني في سكشن لماذا تختار منصتنا',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_why_box3_icon'],
            [
                'setting_value' => 'assets/home/img/icons/ico-why-choose-3.svg',
                'description'   => 'أيقونة البوكس الثالث في سكشن لماذا تختار منصتنا',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'homepage_why_box4_icon'],
            [
                'setting_value' => 'assets/home/img/icons/ico-why-choose-4.svg',
                'description'   => 'أيقونة البوكس الرابع في سكشن لماذا تختار منصتنا',
                'updated_by'    => $adminUserId,
            ]
        );

        // صفحة تواصل معنا - تفاصيل الاتصال

        AdminSetting::firstOrCreate(
            ['setting_key' => 'contact_address'],
            [
                'setting_value' => 'غزة',
                'description'   => 'عنوان المكتب الرئيسي الذي يظهر في صفحة تواصل معنا.',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'contact_phone'],
            [
                'setting_value' => '0970599586158',
                'description'   => 'رقم الهاتف للدعم الفني.',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'contact_hours'],
            [
                'setting_value' => 'من السبت إلى الخميس 8ص إلى 8م',
                'description'   => 'ساعات العمل.',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'contact_email'],
            [
                'setting_value' => 'ahmad@gmail.com',
                'description'   => 'البريد الإلكتروني للتواصل.',
                'updated_by'    => $adminUserId,
            ]
        );

// لو حابة تخلي الأيقونات ديناميكية كمان:
        AdminSetting::firstOrCreate(
            ['setting_key' => 'contact_icon_address'],
            [
                'setting_value' => 'assets/home/img/icons/ico-contact-1.svg',
                'description'   => 'أيقونة العنوان.',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'contact_icon_phone'],
            [
                'setting_value' => 'assets/home/img/icons/ico-contact-2.svg',
                'description'   => 'أيقونة الهاتف.',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'contact_icon_hours'],
            [
                'setting_value' => 'assets/home/img/icons/ico-contact-3.svg',
                'description'   => 'أيقونة ساعات العمل.',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'contact_icon_email'],
            [
                'setting_value' => 'assets/home/img/icons/ico-contact-4.svg',
                'description'   => 'أيقونة البريد الإلكتروني.',
                'updated_by'    => $adminUserId,
            ]
        );

        AdminSetting::firstOrCreate(
            ['setting_key' => 'site_description'],
            [
                'setting_value' => 'منصة تتيح للمستخدمين تأجير واستئجار معدات البناء...',
                'description'   => 'وصف مختصر يظهر في الفوتر و صفحة الرئيسية.',
                'updated_by'    => $adminUserId,
            ]
        );

        $this->command->info('Admin Settings seeded without fake random data!');

    }
}

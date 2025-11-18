<?php
namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $equipmentCategories = EquipmentCategory::where('is_active', true)->with('children')->get(); // تحميل children هنا
        $locations           = ['غزة', 'خان يونس', 'الوسطى', 'الشمال', 'رفح'];

        $featuredCategories = EquipmentCategory::parents()->active()->inRandomOrder()->take(6)->get();

        // $contactPhone    = AdminSetting::where('setting_key', 'contact_phone')->first()->setting_value ?? '+970 59 723 4892';
        // $officeHours     = AdminSetting::where('setting_key', 'office_hours')->first()->setting_value ?? 'السبت - الخميس ( 8ص - 6م)';
        // $contactEmail    = AdminSetting::where('setting_key', 'contact_email')->first()->setting_value ?? 'rentals@my-domain.net';
        // $siteDescription = AdminSetting::where('setting_key', 'site_description')->first()->setting_value ?? 'منصة تتيح للمستخدمين خدمات من تأجير واستئجار معدات بجميع أنواعها وبأسعار مناسبة';

        return view('home', compact(
            'equipmentCategories',
            'locations',
            'featuredCategories',
            // 'contactPhone',
            // 'officeHours',
            // 'contactEmail',
            // 'siteDescription'
        ));
    }

    public function categories(Request $request)
    {
        $parentId = $request->input('parent_id');

        $currentParent = null; // ضمان وجود المتغيّر دائمًا

        if ($parentId) {
            $parentCategory = EquipmentCategory::find($parentId);
            if (! $parentCategory) {
                abort(404);
            }
            $categories    = $parentCategory->children()->active()->with('children')->paginate(12);
            $currentParent = $parentCategory;
        } else {
            $categories = EquipmentCategory::parents()->active()->with('children')->paginate(12);
        }

        return view('frontend.categories', compact('categories', 'currentParent'));
    }

    public function equipments(Request $request)
    {
        $query        = $request->input('query');
        $categoryId   = $request->input('category');
        $location     = $request->input('location');
        $minDailyRate = $request->input('min_daily_rate');
        $maxDailyRate = $request->input('max_daily_rate');

        $equipments = Equipment::query()
            ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->when($categoryId, function ($q) use ($categoryId) {
                $selectedCategory = EquipmentCategory::find($categoryId);
                if ($selectedCategory) {
                    $categoryIds = [$selectedCategory->id];
                    $this->getAllChildrenIds($selectedCategory, $categoryIds);
                    $q->whereIn('category_id', array_unique($categoryIds)); // array_unique لتحسين الأداء وتجنب التكرار
                }
            })
            ->when($location, fn($q) => $q->where('location_address', 'like', "%{$location}%"))
            ->when($minDailyRate, fn($q) => $q->where('daily_rate', '>=', $minDailyRate))
            ->when($maxDailyRate, fn($q) => $q->where('daily_rate', '<=', $maxDailyRate)) // **تصحيح الخطأ الإملائي هنا**
            ->where('is_approved_by_admin', true)
            ->where('status', 'available')
            ->with('owner', 'category', 'images')
            ->paginate(12);

        // هذه المتغيرات ضرورية لفلتر البحث في صفحة المعدات
        $equipmentCategories = EquipmentCategory::where('is_active', true)->with('children')->get();
        $locations           = ['غزة', 'خان يونس', 'الوسطى', 'الشمال', 'رفح'];

        // **تصحيح الـ View الراجع**: الآن يشير إلى 'equipments' بدلاً من 'home'
        // ويمرر جميع المتغيرات اللازمة لتعبئة الفلاتر وعرض النتائج
        return view('frontend.equipments', compact(
            'equipments',
            'equipmentCategories', // تم تغيير اسم المتغير ليتوافق مع الـ View
            'locations',
            'query',
            'categoryId',
            'location',
            'minDailyRate',
            'maxDailyRate'
        ));
    }

    private function getAllChildrenIds(EquipmentCategory $category, array &$ids): void
    {
        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $this->getAllChildrenIds($child, $ids);
        }
    }

    // ================= عن الموقع =================
    public function about()
    {
        $usersCount      = \App\Models\User::count();
        $equipmentsCount = \App\Models\Equipment::count();
        $bookingsCount   = \App\Models\Booking::count();

        $keys = [
            'homepage_steps_subtitle',
            'homepage_steps_title',
            'homepage_step1_title',
            'homepage_step2_title',
            'homepage_step3_title',
            'homepage_step4_title',
            'homepage_step1_icon',
            'homepage_step2_icon',
            'homepage_step3_icon',
            'homepage_step4_icon',

            'homepage_step1_tooltip',
            'homepage_step2_tooltip',
            'homepage_step3_tooltip',
            'homepage_step4_tooltip',

            'homepage_why_subtitle',
            'homepage_why_title',
            'homepage_why_text',
            'homepage_why_box1_title',
            'homepage_why_box2_title',
            'homepage_why_box3_title',
            'homepage_why_box4_title',
            'homepage_why_box1_icon',
            'homepage_why_box2_icon',
            'homepage_why_box3_icon',
            'homepage_why_box4_icon',

            'homepage_cta_title',
            'homepage_cta_text',

        ];

        $settings = AdminSetting::whereIn('setting_key', $keys)
            ->pluck('setting_value', 'setting_key');

        return view('frontend.about', [
            'stepsSubtitle'   => $settings['homepage_steps_subtitle'] ?? '',
            'stepsTitle'      => $settings['homepage_steps_title'] ?? '',
            'step1Title'      => $settings['homepage_step1_title'] ?? '',
            'step2Title'      => $settings['homepage_step2_title'] ?? '',
            'step3Title'      => $settings['homepage_step3_title'] ?? '',
            'step4Title'      => $settings['homepage_step4_title'] ?? '',
            'step1Icon'       => $settings['homepage_step1_icon'] ?? 'assets/home/img/icons/ico-step-1.png',
            'step2Icon'       => $settings['homepage_step2_icon'] ?? 'assets/home/img/icons/ico-step-2.png',
            'step3Icon'       => $settings['homepage_step3_icon'] ?? 'assets/home/img/icons/ico-step-3.png',
            'step4Icon'       => $settings['homepage_step4_icon'] ?? 'assets/home/img/icons/ico-step-4.png',

            'step1Tooltip'    => $settings['homepage_step1_tooltip'] ?? '',
            'step2Tooltip'    => $settings['homepage_step2_tooltip'] ?? '',
            'step3Tooltip'    => $settings['homepage_step3_tooltip'] ?? '',
            'step4Tooltip'    => $settings['homepage_step4_tooltip'] ?? '',

            'whySubtitle'     => $settings['homepage_why_subtitle'] ?? '',
            'whyTitle'        => $settings['homepage_why_title'] ?? '',
            'whyText'         => $settings['homepage_why_text'] ?? '',
            'whyBox1Title'    => $settings['homepage_why_box1_title'] ?? '',
            'whyBox2Title'    => $settings['homepage_why_box2_title'] ?? '',
            'whyBox3Title'    => $settings['homepage_why_box3_title'] ?? '',
            'whyBox4Title'    => $settings['homepage_why_box4_title'] ?? '',
            'whyBox1Icon'     => $settings['homepage_why_box1_icon'] ?? 'assets/home/img/icons/ico-why-choose-1.svg',
            'whyBox2Icon'     => $settings['homepage_why_box2_icon'] ?? 'assets/home/img/icons/ico-why-choose-2.svg',
            'whyBox3Icon'     => $settings['homepage_why_box3_icon'] ?? 'assets/home/img/icons/ico-why-choose-3.svg',
            'whyBox4Icon'     => $settings['homepage_why_box4_icon'] ?? 'assets/home/img/icons/ico-why-choose-4.svg',

            'ctaTitle'        => $settings['homepage_cta_title'] ?? '',
            'ctaText'         => $settings['homepage_cta_text'] ?? '',

            'usersCount'      => $usersCount,
            'equipmentsCount' => $equipmentsCount,
            'bookingsCount'   => $bookingsCount,
        ]);

    }

    // ================= تواصل معنا - عرض الصفحة =================
    public function contact()
    {
        $keys = [
            'contact_address',
            'contact_phone',
            'contact_hours',
            'contact_email',

            // icons
            'contact_icon_address',
            'contact_icon_phone',
            'contact_icon_hours',
            'contact_icon_email',
        ];

        $settings = AdminSetting::whereIn('setting_key', $keys)
            ->pluck('setting_value', 'setting_key')->toArray();
        $user = Auth::user(); // ممكن يكون null لو مش مسجل
        return view('frontend.contact', [
            'contactAddress' => $settings['contact_address'] ?? '',
            'contactPhone'   => $settings['contact_phone'] ?? '',
            'contactHours'   => $settings['contact_hours'] ?? '',
            'contactEmail'   => $settings['contact_email'] ?? '',

            'iconAddress'    => $settings['contact_icon_address'] ?? 'assets/home/img/icons/ico-contact-1.svg',
            'iconPhone'      => $settings['contact_icon_phone'] ?? 'assets/home/img/icons/ico-contact-2.svg',
            'iconHours'      => $settings['contact_icon_hours'] ?? 'assets/home/img/icons/ico-contact-3.svg',
            'iconEmail'      => $settings['contact_icon_email'] ?? 'assets/home/img/icons/ico-contact-4.svg',
            'user'           => $user,
        ]);
    }

    // ================= تواصل معنا - استلام الفورم =================
    public function sendContact(Request $request)
    {
        $user = Auth::user(); // مضمون لأنه داخل middleware auth

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string|min:5',
        ]);

        //  نحاول نجيب الـ admin من إعدادات النظام
        $adminId = AdminSetting::where('setting_key', 'contact_admin_id')->value('setting_value');

        //  لو مش موجود، نجيب أول أدمن من جدول users
        if (! $adminId) {
            // عدلي شرط الدور حسب اللي عندك فعلياً: role / is_admin / type ...
            $adminId = User::where('role', 'admin')->value('id');
            // أو مثلاً:
            // $adminId = User::where('is_admin', true)->value('id');
        }

        //  لو برضو ما لقينا ولا أدمن، نرجّع Error محترم
        if (! $adminId) {
            return back()->withErrors([
                'general' => 'لا يوجد حساب إداري مخصص لاستقبال رسائل التواصل. الرجاء إضافة أدمن أولاً.',
            ]);
        }

        // // نحط الـ subject ببداية الرسالة عشان ما نحتاج نعدّل جدول messages
        // $content = $data['content'];
        // if (! empty($data['subject'])) {
        //     $content = "الموضوع: {$data['subject']}\n\n" . $content;
        // }

        // // $contentParts = [];

        // // $contentParts[] = "المرسل: {$data['name']} ({$data['email']})";

        // // if (! empty($data['subject'])) {
        // //     $contentParts[] = "الموضوع: {$data['subject']}";
        // // }

        // // $contentParts[] = "الرسالة:\n{$data['content']}";

        // // $content = implode("\n\n", $contentParts);

        // نركّب محتوى الرسالة بشكل مرتب
        $content = "المرسل: {$data['name']} ({$data['email']})\n\n";

        if (! empty($data['subject'])) {
            $content .= "الموضوع: {$data['subject']}\n\n";
        }
        $content .= "الرسالة:\n{$data['content']}";


        Message::create([
            'sender_id'      => $user->id,
            'receiver_id'    => $adminId,
            'booking_id'     => null,
            'content'        => $content,
            'message_type'   => 'inquiry',
            'attachment_url' => null,
            // is_read و is_resolved بياخدوا الـ default من الجدول
        ]);

        return back()->with('success', 'تم استلام رسالتك بنجاح، سيتم التواصل معك في أقرب وقت');
    }
}

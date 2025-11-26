<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// تأكد من استيراد الـ AdminSetting Model

class AdminSettingController extends Controller
{
    protected array $protectedKeys = [
        'tax_rate_percent',
        'contact_email',
        'minimum_rental_days',
        'terms_and_conditions_url',
        'maintenance_mode',
        'homepage_steps_subtitle',
        'homepage_steps_title',
        'homepage_step1_title',
        'homepage_step2_title',
        'homepage_step3_title',
        'homepage_step4_title',
        'homepage_why_subtitle',
        'homepage_why_title',
        'homepage_why_text',
        'homepage_why_box1_title',
        'homepage_why_box2_title',
        'homepage_why_box3_title',
        'homepage_why_box4_title',
        'homepage_cta_title',
        'homepage_cta_text',
        'homepage_step1_tooltip',
        'homepage_step2_tooltip',
        'homepage_step3_tooltip',
        'homepage_step4_tooltip',
        'homepage_step1_icon',
        'homepage_step2_icon',
        'homepage_step3_icon',
        'homepage_step4_icon',
        'homepage_why_box1_icon',
        'homepage_why_box2_icon',
        'homepage_why_box3_icon',
        'homepage_why_box4_icon',
        'contact_address',
        'contact_phone',
        'contact_hours',
        'contact_email',
        'contact_icon_address',
        'contact_icon_phone',
        'contact_icon_hours',
        'contact_icon_email',
        'site_description',
    ];
    /**
     * عرض قائمة بجميع إعدادات النظام.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
    $settings = AdminSetting::orderBy('setting_key')->get();
    $protectedKeys = $this->protectedKeys;

    return view('dashboard.settings.index', compact('settings', 'protectedKeys'));
    }
    // صفحة التفاصيل
    public function show(AdminSetting $adminSetting)
    {
        return view('dashboard.settings.show', compact('adminSetting'));
    }

    /**
     * تحديث إعداد نظام معين.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AdminSetting  $adminSetting
     * @return \Illuminate\Http\RedirectResponse
     */
    // public function update(Request $request, AdminSetting $adminSetting)
    // {
    //     $request->validate([
    //         'setting_value' => ['required', 'string'],
    //         // يمكنك إضافة قواعد تحقق أكثر تحديداً لكل نوع إعداد
    //         // مثال: Rule::when($adminSetting->setting_key === 'tax_rate_percent', 'numeric')
    //     ]);

    //     $adminSetting->update([
    //         'setting_value' => $request->setting_value,
    //         'updated_by' => auth()->id(), // تسجيل المستخدم الذي قام بالتحديث
    //     ]);

    //     return redirect()->route('admin.settings.index')->with('success', 'تم تحديث الإعداد بنجاح.');
    // }
    public function update(Request $request, AdminSetting $adminSetting)
    {
        $iconKeys = [
            'homepage_step1_icon',
            'homepage_step2_icon',
            'homepage_step3_icon',
            'homepage_step4_icon',
            'homepage_why_box1_icon',
            'homepage_why_box2_icon',
            'homepage_why_box3_icon',
            'homepage_why_box4_icon',
        ];

        // لو الإعداد من نوع "صورة"
        if (in_array($adminSetting->setting_key, $iconKeys)) {

            $request->validate([
                'setting_file' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            ]);

            if ($request->hasFile('setting_file')) {

                // احذف القديمة لو كانت مخزنة في storage
                if ($adminSetting->setting_value
                    && str_starts_with($adminSetting->setting_value, 'storage/')) {

                    $oldPath = str_replace('storage/', '', $adminSetting->setting_value);
                    Storage::disk('public')->delete($oldPath);
                }

                                                                                           // خزّن الجديدة في /storage/settings/icons
                $path = $request->file('setting_file')->store('settings/icons', 'public'); // يرجع settings/icons/xxxx.png

                // نخزن القيمة بصيغة "storage/..." عشان asset() يشتغل معها مباشرة
                $adminSetting->setting_value = 'storage/' . $path;
            }

        } else {
            // باقي الإعدادات العادية (نص/رقم...إلخ)
            $data = $request->validate([
                'setting_value' => 'required|string',
            ]);

            $adminSetting->setting_value = $data['setting_value'];
        }

        $adminSetting->updated_by = Auth::id();
        $adminSetting->save();

        return back()->with('success', 'تم تحديث الإعداد بنجاح');
    }
    public function backup()
    {
        // نجيب كل الإعدادات
        $settings = AdminSetting::orderBy('setting_key')->get();

        // نجهز الداتا اللي بدنا نخزنها
        $data = [
            'generated_at' => now()->toDateTimeString(),
            'generated_by' => Auth::user() ? Auth::user()->only(['id', 'first_name', 'last_name', 'email']) : null,
            'settings'     => $settings->toArray(),
        ];

        // اسم الملف: backups/admin_settings_2025-11-18_12-30-15.json
        $fileName = 'backups/admin_settings_' . now()->format('Y-m-d_H-i-s') . '.json';

        // نخزن الملف في storage/app/backups
        Storage::disk('local')->put($fileName, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return back()->with(
            'success',
            'تم إنشاء نسخة احتياطية من إعدادات النظام وحفظها في مجلد التخزين (storage/app/backups).'
        );
    }

// يمكنك إضافة دوال store و destroy هنا إذا كنت تريد السماح للمشرفين بإنشاء أو حذف الإعدادات
/*
    public function store(Request $request)
    {
        $request->validate([
            'setting_key' => ['required', 'string', 'unique:admin_settings', 'max:255'],
            'setting_value' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        AdminSetting::create([
            'setting_key' => $request->setting_key,
            'setting_value' => $request->setting_value,
            'description' => $request->description,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'تم إضافة إعداد جديد بنجاح.');
    }

    public function destroy(AdminSetting $adminSetting)
    {
        // قد ترغب في منع حذف بعض الإعدادات الأساسية
        if (in_array($adminSetting->setting_key, ['tax_rate_percent', 'contact_email', 'minimum_rental_days'])) {
             return redirect()->route('admin.settings.index')->with('error', 'لا يمكن حذف هذا الإعداد الأساسي.');
        }

        $adminSetting->delete();
        return redirect()->route('admin.settings.index')->with('success', 'تم حذف الإعداد بنجاح.');
    }
    */

    public function trash(Request $request)
    {
        $query = $request->input('query');

        $settings = AdminSetting::onlyTrashed()
            ->when($query, function ($q, $query) {
                $q->where('setting_key', 'like', "%{$query}%")
                    ->orWhere('setting_value', 'like', "%{$query}%");
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('dashboard.settings.trash', compact('settings', 'query'));
    }

    // public function destroy(AdminSetting $adminSetting)
    // {
    //     // لو في إعدادات أساسية ما بدك تنحذف:
    //     if (in_array($adminSetting->setting_key, ['tax_rate_percent', 'contact_email'])) {
    //         return back()->with('error', 'لا يمكن حذف هذا الإعداد الأساسي.');
    //     }

    //     $adminSetting->delete(); // Soft delete لو مفعّلة في الموديل

    //     return back()->with('success', 'تم نقل الإعداد إلى سلة المحذوفات.');
    // }
    public function destroy(AdminSetting $adminSetting)
    {
        if (in_array($adminSetting->setting_key, $this->protectedKeys)) {
            return back()->with('error', 'لا يمكن حذف هذا الإعداد الأساسي من النظام.');
        }

        $adminSetting->delete();

        return back()->with('success', 'تم نقل الإعداد إلى سلة المحذوفات.');
    }

    public function restore($id)
    {
        $setting = AdminSetting::onlyTrashed()->findOrFail($id);
        $setting->restore();

        return redirect()->route('admin.settings.trash')->with('success', 'تم استعادة الإعداد.');
    }

    public function restoreAll()
    {
        AdminSetting::onlyTrashed()->restore();

        return redirect()->route('admin.settings.trash')->with('success', 'تم استعادة جميع الإعدادات المحذوفة.');
    }

    public function forceDelete($id)
    {
        $setting = AdminSetting::onlyTrashed()->findOrFail($id);
        // $setting->forceDelete();

        // return redirect()->route('admin.settings.trash')->with('success', 'تم حذف الإعداد نهائياً.');

        if (in_array($setting->setting_key, $this->protectedKeys)) {
            return back()->with('error', 'لا يمكن حذف هذا الإعداد الأساسي نهائيًا.');
        }

        // لو الإعداد صورة نخلي عندك خيار حذفها
        if ($setting->setting_value && str_starts_with($setting->setting_value, 'storage/')) {
            $oldPath = str_replace('storage/', '', $setting->setting_value);
            Storage::disk('public')->delete($oldPath);
        }

        $setting->forceDelete();

        return back()->with('success', 'تم حذف الإعداد نهائيًا.');
    }

    public function forceDeleteAll()
    {
        // AdminSetting::onlyTrashed()->forceDelete();

        // return back()->with('success', 'تم حذف جميع الإعدادات المحذوفة نهائياً.');
        $settings = AdminSetting::onlyTrashed()->get();

        foreach ($settings as $setting) {
            if (in_array($setting->setting_key, $this->protectedKeys)) {
                continue; // ما نحذف المحمي
            }

            if ($setting->setting_value && str_starts_with($setting->setting_value, 'storage/')) {
                $oldPath = str_replace('storage/', '', $setting->setting_value);
                Storage::disk('public')->delete($oldPath);
            }

            $setting->forceDelete();
        }

        return back()->with('success', 'تم حذف جميع الإعدادات (غير المحمية) نهائيًا.');
    }

}

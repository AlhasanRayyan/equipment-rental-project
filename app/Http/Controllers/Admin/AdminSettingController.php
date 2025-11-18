<?php
namespace App\Http\Controllers\Admin;

use App\Models\AdminSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request; // تأكد من استيراد الـ AdminSetting Model

class AdminSettingController extends Controller
{
    /**
     * عرض قائمة بجميع إعدادات النظام.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $settings = AdminSetting::orderBy('setting_key')->get();

        return view('dashboard.settings.index', compact('settings'));
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
};

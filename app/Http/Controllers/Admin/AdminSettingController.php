<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminSetting; // تأكد من استيراد الـ AdminSetting Model
use Illuminate\Validation\Rule;

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
    public function update(Request $request, AdminSetting $adminSetting)
    {
        $request->validate([
            'setting_value' => ['required', 'string'],
            // يمكنك إضافة قواعد تحقق أكثر تحديداً لكل نوع إعداد
            // مثال: Rule::when($adminSetting->setting_key === 'tax_rate_percent', 'numeric')
        ]);

        $adminSetting->update([
            'setting_value' => $request->setting_value,
            'updated_by' => auth()->id(), // تسجيل المستخدم الذي قام بالتحديث
        ]);

        return redirect()->route('admin.settings.index')->with('success', 'تم تحديث الإعداد بنجاح.');
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
}
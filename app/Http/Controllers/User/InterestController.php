<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    // عرض صفحة الاهتمامات
    public function edit()
    {
        $categories = EquipmentCategory::whereNull('parent_id')->where('is_active', true)->get();
        $userInterests = auth()->user()->interests->pluck('id')->toArray();

        return view('user.interests.edit', compact('categories', 'userInterests'));
    }

    // حفظ الاهتمامات
    public function update(Request $request)
    {
        $request->validate([
            'category_user'   => ['nullable', 'array'],
            'category_user.*' => ['exists:categories,id'],
        ]);

        auth()->user()->interests()->sync($request->interests ?? []);

        return back()->with('success', 'تم حفظ اهتماماتك بنجاح!');
    }
}

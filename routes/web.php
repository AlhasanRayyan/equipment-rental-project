<?php

use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EquipmentCategoryController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Equipment\EquipmentsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OwnerEquipmentController;
use App\Http\Controllers\User\UserProfileController;
use Illuminate\Support\Facades\Route;


include 'admin.php' ;

// 1. Frontend / Public Website Routes
// ========================================================================
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/categories', [HomeController::class, 'categories'])->name('categories');
Route::get('/equipments', [HomeController::class, 'equipments'])->name('equipments'); // صفحة نتائج البحث
Route::get('/equipments/create', [EquipmentsController::class, 'create'])->name('equipments.create');
Route::post('/equipments/store', [EquipmentsController::class, 'store'])->name('equipments.store');
// صفحة تعديل معدة موجودة
Route::get('/equipments/{id}/edit', [EquipmentsController::class, 'edit'])->name('equipments.edit');
// تحديث بيانات المعدة
Route::put('/equipments/{id}', [EquipmentsController::class, 'update'])->name('equipments.update');
Route::get('/equipments/{id}', [EquipmentsController::class, 'show'])->name('equipments.show');

// مع ولا بدون المصادقة
Route::get('/owner/equipments', [OwnerEquipmentController::class, 'index'])->name('owner.equipments');
Route::get('/owner/equipments/search', [OwnerEquipmentController::class, 'search'])->name('owner.equipments.search');
Route::get('/owner/equipments/{id}/edit', [OwnerEquipmentController::class, 'edit'])->name('owner.equipments.edit');

Route::get('/about', [HomeController::class, 'about'])->name('about');
// صفحة تواصل معنا: GET للجميع، POST للمسجلين فقط
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'sendContact'])->middleware('auth')->name('contact.send');


// للأسئلة الشائعة
Route::view('/faq', 'frontend.faq')->name('faq');
// Route User
Route::middleware(['auth', 'user'])->group(function () {
// Route::resource('profile', UserProfileController::class);
// تجهيز وإدارة المعدات الخاصة بالمستخدم
    // Route::get('/equipments/create', [EquipmentsController::class, 'create'])->name('equipments.create');
    // Route::post('/equipments/store', [EquipmentsController::class, 'store'])->name('equipments.store');
    // Route::get('/equipments/{id}/edit', [EquipmentsController::class, 'edit'])->name('equipments.edit');
    // Route::put('/equipments/{id}', [EquipmentsController::class, 'update'])->name('equipments.update');

    // معدات المالك
    // Route::get('/owner/equipments', [OwnerEquipmentController::class, 'index'])->name('owner.equipments');
    // Route::get('/owner/equipments/search', [OwnerEquipmentController::class, 'search'])->name('owner.equipments.search');
    // Route::get('/owner/equipments/{id}/edit', [OwnerEquipmentController::class, 'edit'])->name('owner.equipments.edit');

    // الملف الشخصي
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/section/{type}', [UserProfileController::class, 'loadSection'])->name('profile.section');
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [UserProfileController::class, 'update'])->name('profile.update');
});

require __DIR__ . '/auth.php'; // تأكد من وجود هذا السطر

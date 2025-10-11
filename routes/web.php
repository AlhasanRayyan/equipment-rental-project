<?php

use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EquipmentCategoryController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


// 1. Frontend / Public Website Routes
// ========================================================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/categories', [HomeController::class, 'categories'])->name('categories');
Route::get('/equipments', [HomeController::class, 'equipments'])->name('equipments');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // مسار لوحة التحكم الرئيسية (لوحة تحكم المشرف)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // مسارات إدارة المستخدمين
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::post('/{user}/activate', [UserController::class, 'activate'])->name('activate');
        Route::post('/{user}/deactivate', [UserController::class, 'deactivate'])->name('deactivate');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });


    // مسارات إدارة المعدات (مراجعة الإعلانات والموافقة)
    Route::prefix('equipment')->name('equipment.')->group(function () {
        Route::get('/', [EquipmentController::class, 'index'])->name('index');
        Route::get('/{equipment}', [EquipmentController::class, 'show'])->name('show');

        Route::post('/{equipment}/approve', [EquipmentController::class, 'approve'])->name('approve');
        Route::post('/{equipment}/reject', [EquipmentController::class, 'reject'])->name('reject'); // خيار لرفض المعدة
        Route::delete('/{equipment}', [EquipmentController::class, 'destroy'])->name('destroy'); // حذف المعدة
    });

    // مسارات إدارة الشكاوى والاستفسارات
    Route::prefix('complaints')->name('complaints.')->group(function () {
        Route::get('/', [ComplaintController::class, 'index'])->name('index');
        Route::get('/{message}', [ComplaintController::class, 'show'])->name('show'); // لعرض تفاصيل شكوى
        Route::post('/{message}/mark-as-read', [ComplaintController::class, 'markAsRead'])->name('markAsRead'); // لتمييز الشكوى كمقروءة
        Route::post('/{message}/resolve', [ComplaintController::class, 'resolve'])->name('resolve'); // لحل الشكوى
        Route::delete('/{message}', [ComplaintController::class, 'destroy'])->name('destroy'); // لحذف شكوى
    });

    // مسارات إدارة إعدادات النظام
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminSettingController::class, 'index'])->name('index');
        Route::put('/{adminSetting}', [AdminSettingController::class, 'update'])->name('update'); // لتعديل إعداد معين
    });

    // مسارات إدارة فئات المعدات
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [EquipmentCategoryController::class, 'index'])->name('index');
        Route::post('/', [EquipmentCategoryController::class, 'store'])->name('store');
        Route::put('/{equipmentCategory}', [EquipmentCategoryController::class, 'update'])->name('update');
        Route::delete('/{equipmentCategory}', [EquipmentCategoryController::class, 'destroy'])->name('destroy');
        Route::get('/{equipmentCategory}/equipment', [EquipmentCategoryController::class, 'showEquipment'])->name('showEquipment'); // **جديد: عرض المعدات المرتبطة بفئة**
    });
});


require __DIR__ . '/auth.php'; // تأكد من وجود هذا السطر

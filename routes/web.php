<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\UserController;
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
        Route::post('/{equipment}/approve', [EquipmentController::class, 'approve'])->name('approve');
        Route::post('/{equipment}/reject', [EquipmentController::class, 'reject'])->name('reject'); // خيار لرفض المعدة
        Route::delete('/{equipment}', [EquipmentController::class, 'destroy'])->name('destroy'); // حذف المعدة
    });
});


require __DIR__ . '/auth.php'; // تأكد من وجود هذا السطر

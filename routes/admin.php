<?php

use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EquipmentCategoryController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

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
        Route::get('/{user}/show', [UserController::class, 'show'])->name('show');

        // سلة المحذوفات
        Route::get('/trash', [UserController::class, 'trash'])->name('trash');

        Route::post('/trash/{id}/restore', [UserController::class, 'restore'])->name('restore');
        Route::post('/trash/restore-all', [UserController::class, 'restoreAll'])->name('restoreAll');

        Route::delete('/trash/{id}/force-delete', [UserController::class, 'forceDelete'])->name('forceDelete');
        Route::delete('/trash/force-delete-all', [UserController::class, 'forceDeleteAll'])->name('forceDeleteAll');

    });

    // مسارات إدارة المعدات (مراجعة الإعلانات والموافقة)
    Route::prefix('equipment')->name('equipment.')->group(function () {
        //  حطّي stats قبل /{equipment} عشان ما يتفسّر كـ ID
        Route::get('/stats', [EquipmentController::class, 'stats'])->name('stats');

        Route::get('/', [EquipmentController::class, 'index'])->name('index');
        Route::get('/{equipment}', [EquipmentController::class, 'show'])->name('show');

        Route::post('/{equipment}/approve', [EquipmentController::class, 'approve'])->name('approve');
        Route::post('/{equipment}/reject', [EquipmentController::class, 'reject'])->name('reject'); // خيار لرفض المعدة
        Route::delete('/{equipment}', [EquipmentController::class, 'destroy'])->name('destroy');    // حذف المعدة

        //  سلة المحذوفات
        Route::get('/trash', [EquipmentController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [EquipmentController::class, 'restore'])->name('restore');
        Route::post('/restore-all', [EquipmentController::class, 'restoreAll'])->name('restoreAll');
        Route::delete('/{id}/force-delete', [EquipmentController::class, 'forceDelete'])->name('forceDelete');
        Route::delete('/force-delete-all', [EquipmentController::class, 'forceDeleteAll'])->name('forceDeleteAll');

    });

    // مسارات إدارة الشكاوى والاستفسارات
    Route::prefix('complaints')->name('complaints.')->group(function () {
        //  نحطّ الأرشيف قبل '/{message}' عشان ما ينفهم كـ ID
        Route::get('/archived', [ComplaintController::class, 'archived'])->name('archived');
        Route::get('/', [ComplaintController::class, 'index'])->name('index');
        Route::get('/{message}', [ComplaintController::class, 'show'])->name('show');                           // لعرض تفاصيل شكوى
        Route::post('/{message}/mark-as-read', [ComplaintController::class, 'markAsRead'])->name('markAsRead'); // لتمييز الشكوى كمقروءة
        Route::post('/{message}/resolve', [ComplaintController::class, 'resolve'])->name('resolve');            // لحل الشكوى
        Route::delete('/{message}', [ComplaintController::class, 'destroy'])->name('destroy');                  // لحذف شكوى

        // سلة المحذوفات
        Route::get('/trash', [ComplaintController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore-from-trash', [ComplaintController::class, 'restore'])->name('restore');
        Route::post('/restore-all', [ComplaintController::class, 'restoreAll'])->name('restoreAll');
        Route::delete('/{id}/force-delete', [ComplaintController::class, 'forceDelete'])->name('forceDelete');
        Route::delete('/force-delete-all', [ComplaintController::class, 'forceDeleteAll'])->name('forceDeleteAll');
    });

    // مسارات إدارة إعدادات النظام
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminSettingController::class, 'index'])->name('index');
        Route::put('/{adminSetting}', [AdminSettingController::class, 'update'])->name('update'); // لتعديل إعداد معين

        //  سلة المحذوفات
        Route::delete('/{setting}', [AdminSettingController::class, 'destroy'])->name('destroy');                  // لحذف شكوى
        Route::get('/trash', [AdminSettingController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [AdminSettingController::class, 'restore'])->name('restore');
        Route::post('/restore-all', [AdminSettingController::class, 'restoreAll'])->name('restoreAll');
        Route::delete('/{id}/force-delete', [AdminSettingController::class, 'forceDelete'])->name('forceDelete');
        Route::delete('/force-delete-all', [AdminSettingController::class, 'forceDeleteAll'])->name('forceDeleteAll');

        //  مسار إنشاء نسخة احتياطية من إعدادات النظام
        Route::post('/backup', [AdminSettingController::class, 'backup'])->name('backup');
    });

    // مسارات إدارة فئات المعدات
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [EquipmentCategoryController::class, 'index'])->name('index');
        Route::post('/', [EquipmentCategoryController::class, 'store'])->name('store');
        Route::put('/{equipmentCategory}', [EquipmentCategoryController::class, 'update'])->name('update');
        Route::delete('/{equipmentCategory}', [EquipmentCategoryController::class, 'destroy'])->name('destroy');
        //  صفحة إحصائيات الفئات
        Route::get('/stats', [EquipmentCategoryController::class, 'stats'])->name('stats');
                                                                                                                                    // عرض المعدات المرتبطة بالفئة
        Route::get('/{equipmentCategory}/equipment', [EquipmentCategoryController::class, 'showEquipment'])->name('showEquipment'); // **جديد: عرض المعدات المرتبطة بفئة**

        //  سلة المحذوفات
        Route::get('/trash', [EquipmentCategoryController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [EquipmentCategoryController::class, 'restore'])->name('restore');
        Route::post('/restore-all', [EquipmentCategoryController::class, 'restoreAll'])->name('restoreAll');
        Route::delete('/{id}/force-delete', [EquipmentCategoryController::class, 'forceDelete'])->name('forceDelete');
        Route::delete('/force-delete-all', [EquipmentCategoryController::class, 'forceDeleteAll'])->name('forceDeleteAll');

    });
});

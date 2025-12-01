<?php

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// يجب أن يكون المستخدم مسجلاً الدخول لاستخدام هذه الـ APIs
Route::middleware('auth:sanctum')->group(function () {
    // جلب قائمة المحادثات للمستخدم الحالي
    Route::get('/conversations', [ChatController::class, 'index']);
    // جلب الرسائل لمحادثة معينة
    Route::get('/conversations/{conversation}/messages', [ChatController::class, 'showMessages']);
    // بدء محادثة جديدة (إذا لم تكن موجودة) أو جلب الموجودة
    Route::post('/conversations', [ChatController::class, 'startOrGetConversation']);
    // إرسال رسالة في محادثة معينة
    Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage']);
    // (اختياري) تحديد الرسائل كمقروءة (إذا لم تستخدم التحديد التلقائي في showMessages)
    // Route::post('/messages/{message}/read', [ChatController::class, 'markAsRead']);
    Route::get('/conversations/{conversation}', [ChatController::class, 'getConversationDetails']);
});

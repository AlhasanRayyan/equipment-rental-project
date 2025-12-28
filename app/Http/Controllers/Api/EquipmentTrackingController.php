<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EquipmentTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EquipmentTrackingController extends Controller
{
    /**
     * تخزين سجل تتبع جديد
     */
    public function store(Request $request)
    {
        // 1. التحقق من صحة البيانات المرسلة (Validation)
        $validator = Validator::make($request->all(), [
            'equipment_id'  => 'required|exists:equipment,id', // يجب أن يكون المعدّة موجودة في جدول equipment
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'speed'         => 'nullable|numeric|min:0',
            'battery_level' => 'nullable|numeric|between:0,100',
            'status'        => 'required|string|in:online,offline,moving,idle,maintenance', // حسب الـ Enum عندك
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // 2. إنشاء السجل في قاعدة البيانات
            $tracking = EquipmentTracking::create([
                'equipment_id'  => $request->equipment_id,
                'latitude'      => $request->latitude,
                'longitude'     => $request->longitude,
                'speed'         => $request->speed ?? 0,
                'battery_level' => $request->battery_level,
                'status'        => $request->status,
            ]);

            // 3. إرجاع استجابة نجاح
            return response()->json([
                'status'  => 'success',
                'message' => 'Tracking data recorded successfully',
                'data'    => $tracking
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to save data',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
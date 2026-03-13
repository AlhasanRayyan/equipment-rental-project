<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EquipmentTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Equipment;
use App\Models\User;
use App\Notifications\EquipmentMovedNotification;
use App\Notifications\AppAlertNotification;
use  App\Services\NotificationService;

class EquipmentTrackingController extends Controller
{
    // إعدادات الحركة
    private float $moveThresholdKm = 0.05; // 50 متر = 0.05 كم
    private int $cooldownMinutes = 5;          // لا تعيد نفس الإشعار لنفس المعدة خلال 5 دقائق


    /**
     * تخزين سجل تتبع جديد
     */
    public function store(Request $request)
    {
        //  التحقق من صحة البيانات المرسلة (Validation)
        $validator = Validator::make($request->all(), [
            'equipment_id'  => 'required|exists:equipment,id', // يجب أن يكون المعدّة موجودة في جدول equipment
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'speed'         => 'nullable|numeric|min:0',
            'battery_level' => 'nullable|numeric|between:0,100',
            'status'        => 'required|string|in:online,offline,moving,idle', // حسب الـ Enum عندك

            'start_time'    => 'nullable|date',
            'end_time'      => 'nullable|date|after_or_equal:start_time',
            'duration'      => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // قبل ما نخزن: هات آخر نقطة
            $last = EquipmentTracking::where('equipment_id', $request->equipment_id)
                ->latest('id')
                ->first();
            //  إنشاء السجل في قاعدة البيانات
            $tracking = EquipmentTracking::create([
                'equipment_id'  => $request->equipment_id,
                'latitude'      => $request->latitude,
                'longitude'     => $request->longitude,
                'speed'         => $request->speed ?? 0,
                'battery_level' => $request->battery_level,
                'status'        => $request->status,
                'start_time'    => $request->start_time,
                'end_time'      => $request->end_time,
                'duration'      => $request->duration,
            ]);
              //  بعد التخزين: فحص الحركة + إشعار
            if ($last) {
                $distanceKm = $this->haversineKm(
                    (float)$last->latitude,
                    (float)$last->longitude,
                    (float)$tracking->latitude,
                    (float)$tracking->longitude
                );

                if ($distanceKm >= $this->moveThresholdKm) {
                    $this->notifyIfNotSpam(
                        equipmentId: (int)$request->equipment_id,
                        latitude: (float)$tracking->latitude,
                        longitude: (float)$tracking->longitude,
                        speed: (float)($tracking->speed ?? 0),
                        distanceKm: (float)$distanceKm
                    );
                }
            }

            //  إرجاع استجابة نجاح
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
    /**
     * إرسال إشعار عند الحركة + cooldown لمنع التكرار
     */
    private function notifyIfNotSpam(int $equipmentId, float $latitude, float $longitude, float $speed, float $distanceKm): void
    {
        $equipment = Equipment::with('owner')->find($equipmentId);
        if (!$equipment || !$equipment->owner) return;

        //   لا تعيدي نفس إشعار الحركة لنفس المعدة خلال X دقائق
        $recent = $equipment->owner->notifications()
            ->where('created_at', '>=', now()->subMinutes($this->cooldownMinutes))
            ->where('data->kind', 'equipment_moved')
            ->where('data->equipment_id', $equipmentId)
            ->exists();

        if ($recent) return;

        //  سطر واحد… بس السيرفس رح يرسل للمالك + للأدمن كمان
        NotificationService::equipmentMoved(
            equipment: $equipment,
            distanceKm: $distanceKm,
            lat: $latitude,
            lng: $longitude,
            speed: $speed
        );
    }

    private function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earth = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earth * $c; // KM
    }
}

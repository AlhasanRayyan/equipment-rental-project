<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\EquipmentTracking;

class FetchImuFromFirebase extends Command
{
    protected $signature = 'firebase:fetch:imu';
    protected $description = 'Determine equipment working status from IMU';

    public function handle()
    {
        $deviceId = '1020BA465FF4';

        $url = "https://gps-data-live-default-rtdb.europe-west1.firebasedatabase.app/devices/{$deviceId}/telemetry/latest/imu.json";

        $response = Http::get($url);

        if (!$response->ok()) {
            $this->error('Failed to fetch IMU');
            return;
        }

        $imu = $response->json();
        $acc = $imu['acc'] ?? null;

        if (!$acc) {
            $this->info('No accelerometer data');
            return;
        }

        $magnitude = sqrt(
            pow($acc['x'], 2) +
            pow($acc['y'], 2) +
            pow($acc['z'], 2)
        );

        $isWorking = $magnitude > 1.2; // حد التشغيل

        $last = EquipmentTracking::where('equipment_id', $deviceId)
            ->latest()
            ->first();


        if ($isWorking && (!$last || $last->status === 'off')) {
            EquipmentTracking::create([
                'equipment_id' => $deviceId,
                'status'       => 'on',
                'start_time'   => now(),
            ]);

            $this->info('Equipment started working');
        }


        if (!$isWorking && $last && $last->status === 'on') {
            $last->update([
                'status'           => 'off',
                'end_time'         => now(),
                'duration' => now()->diffInSeconds($last->start_time),
            ]);

            $this->info('Equipment stopped');
        }
    }
}

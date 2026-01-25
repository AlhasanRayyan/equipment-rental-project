<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\EquipmentTracking;

class FetchGpsFromFirebase extends Command
{
    protected $signature = 'firebase:fetch:gps';
    protected $description = 'Fetch GPS data from Firebase';

    public function handle()
    {
        $deviceId = '1020BA465FF4';

        $url = "https://gps-data-live-default-rtdb.europe-west1.firebasedatabase.app/devices/{$deviceId}/telemetry/latest/gps.json";

        $response = Http::get($url);

        if (!$response->ok()) {
            $this->error('Failed to fetch GPS');
            return;
        }

        $gps = $response->json();

        if (!($gps['hasFix'] ?? false)) {
            $this->info('GPS has no fix');
            return;
        }

        EquipmentTracking::create([
            'equipment_id' => $deviceId,
            'latitude'     => $gps['lat'] ?? null,
            'longitude'    => $gps['lon'] ?? null,
        ]);

        $this->info('GPS location saved');
    }
}
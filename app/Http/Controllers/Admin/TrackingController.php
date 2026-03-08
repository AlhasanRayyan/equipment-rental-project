<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EquipmentTracking;

class TrackingController extends Controller
{
    public function index()
    {
        $trackings = EquipmentTracking::with('equipment')
        // احداثيات غزة
            ->whereBetween('latitude', [31.20, 31.70])
            ->whereBetween('longitude', [34.20, 34.60])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('dashboard.tracking.index', compact('trackings'));
    }
}
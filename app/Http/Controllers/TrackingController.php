<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentTracking;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    public function index()
    {
        $ownerEquipmentsCount = Equipment::where('owner_id', Auth::id())->count();

        $trackings = EquipmentTracking::with('equipment')
            ->whereHas('equipment', function ($query) {
                $query->where('owner_id', Auth::id());
            })
            ->whereBetween('latitude', [31.20, 31.70])
            ->whereBetween('longitude', [34.20, 34.60])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('frontend.ownerTracking', compact('trackings', 'ownerEquipmentsCount'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\EquipmentCategory;

class OwnerEquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::where('owner_id', auth()->id())->paginate(6);
        $categories = EquipmentCategory::all();
        return view('user.my_equipments', compact('equipments', 'categories'));
    }

    public function search(Request $request)
    {
        $query = Equipment::where('owner_id', auth()->id());

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('q')) {
            $query->where('name', 'like', "%{$request->q}%");
        }

        $equipments = $query->paginate(6);
        $categories = EquipmentCategory::all();

        return view('user.my_equipments', compact('equipments', 'categories'));
    }

    public function edit($id)
    {
 $equipment = Equipment::with('images')->findOrFail($id);
        $categories = EquipmentCategory::all();
          return view('frontend.equipments.edit', compact('equipment' , 'categories'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\UserFavorite;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // تأكد من وجود هذه

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'الرجاء تسجيل الدخول لعرض المفضلة.');
        }

        $user = Auth::user();
        $query = $user->favorites()->with(['equipment.owner', 'equipment.images', 'equipment.category']);

        if ($request->filled('category')) {
            $query->whereHas('equipment', function ($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        if ($request->filled('equipment_name')) {
            $query->whereHas('equipment', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->equipment_name . '%');
            });
        }

        $favorites = $query->paginate(9);
        $categories = EquipmentCategory::all();

        return view('frontend.favorites', compact('favorites', 'categories'));
    }

    public function destroy(UserFavorite $favorite)
    {
        if (Auth::id() !== $favorite->user_id) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية لحذف هذا العنصر.');
        }

        $favorite->delete();

        return redirect()->back()->with('success', 'تمت إزالة المعدة من المفضلة بنجاح.');
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
        ]);

        $user = Auth::user();
        $equipmentId = $request->input('equipment_id');

        $favorite = UserFavorite::where('user_id', $user->id)
                                ->where('equipment_id', $equipmentId)
                                ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'removed', 'message' => 'تمت الإزالة من المفضلة.']);
        } else {
            UserFavorite::create([
                'user_id' => $user->id,
                'equipment_id' => $equipmentId,
            ]);
            return response()->json(['status' => 'added', 'message' => 'تمت الإضافة إلى المفضلة.']);
        }
    }
}
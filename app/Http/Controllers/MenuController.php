<?php

namespace App\Http\Controllers;

use App\Events\DataUpdated;
use App\Models\Category;
use App\Models\Menu;
use App\Models\SubCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{

    public function index()
    {
        if (!Auth::check()) return redirect()->route('login');

        // Pastikan memanggil 'sub_category' (sesuai nama fungsi di model Menu)
        $groupedCategories = Category::with('subCategories.menus.sub_category')->get();

        $products = Menu::with('sub_category')->get(); // Jika butuh relasi di sini juga

        $view = Auth::user()->role_id == 1 ? 'Admin.index' : 'User.index';

        return view($view, compact('groupedCategories', 'products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('', compact('categories'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|integer|min:0',
                'sub_category_id' => 'required|exists:sub_categories,id', // Sesuai garis di ERD
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $data = $request->all();
            $data['count_sold'] = 0;
            $data['status'] = $request->status ?? 'available';

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('menus', 'public');
                $data['image'] = $path;
                $data['image_url'] = Storage::url($path);
            }

            // Simpan ke DB (Laravel cuma bakal masukin field yang ada di $fillable)
            $menu = \App\Models\Menu::create($data);
            
            Log::info('Triggering event for: ' . $request->name);
            // Trigger Realtime Reverb
            event(new \App\Events\DataUpdated('menu'));

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function edit(Menu $menu)
    {
        $categories = Category::all();
        return view('menus.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $menu = \App\Models\Menu::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer',
            'sub_category_id' => 'required|exists:sub_categories,id',
        ]);

        $data = $request->only(['name', 'price', 'sub_category_id', 'desc']);

        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            
            $path = $request->file('image')->store('menus', 'public');
            $data['image'] = $path;
            $data['image_url'] = Storage::url($path);
        }

        $menu->update($data);

        // Broadcast update agar layar customer juga berubah harganya/gambarnya
        event(new \App\Events\DataUpdated('menu_updated'));

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        try {
            $menu = \App\Models\Menu::findOrFail($id);
            
            // Hapus gambar dari storage jika ada
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }

            $menu->delete();

            // Trigger Realtime agar menu hilang di layar semua orang
            event(new \App\Events\DataUpdated('menu_deleted'));

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $menu = \App\Models\Menu::findOrFail($id);
            
            // Gunakan 'unavailable' bukannya 'not available'
            $menu->status = ($menu->status === 'available') ? 'unavailable' : 'available';
            $menu->save();

            if (class_exists('\App\Events\DataUpdated')) {
                event(new \App\Events\DataUpdated('menu_status_changed'));
            }

            return response()->json([
                'success' => true, 
                'new_status' => $menu->status
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}


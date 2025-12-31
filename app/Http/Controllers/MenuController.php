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

            return redirect()->back()->with('success', 'Menu berhasil ditambahkan!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Gagal simpan: ' . $e->getMessage()]);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|integer|min:0',
                'sub_category_id' => 'required|exists:sub_categories,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $menu = \App\Models\Menu::findOrFail($id);
            $menu->name = $request->name;
            $menu->price = $request->price;
            $menu->sub_category_id = $request->sub_category_id;
            $menu->status = $request->status ?? $menu->status;

            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($menu->image) {
                    Storage::disk('public')->delete($menu->image);
                }

                $path = $request->file('image')->store('menus', 'public');
                $menu->image = $path;
                $menu->image_url = Storage::url($path);
            }

            $menu->save();

            // Trigger Realtime agar semua orang tahu ada update menu
            event(new \App\Events\DataUpdated('menu_updated'));

            return redirect()->back()->with('success', 'Menu berhasil diupdate!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Gagal update: ' . $e->getMessage()]);
        }
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

            return redirect()->back()->with('success', 'Menu berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Gagal hapus: ' . $e->getMessage()]);
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

            return response()->json(['success' => true, 'new_status' => $menu->status]);
        } catch (\Exception $e) {
            Log::error('Error toggling menu status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengubah status: ' . $e->getMessage()], 500);
        }
    }
}


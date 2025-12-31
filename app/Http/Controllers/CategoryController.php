<?php

namespace App\Http\Controllers;

use App\Events\DataUpdated;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
      public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $category = new \App\Models\Category();
            $category->name = $request->name;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/categories'), $filename);
                $category->image = 'uploads/categories/' . $filename;
            }

            
            $category->save();

            event(new DataUpdated('category'));
            return redirect()->back()->with('success', 'Kategori Berhasil Ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Gagal simpan: ' . $e->getMessage()]);
        }
    }
    public function update(Request $request, $id)
    {
        try{
            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,'.$id,
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
    
            $category = \App\Models\Category::findOrFail($id);
            $category->name = $request->name;
    
            if ($request->hasFile('image')) {
                // Hapus file lama jika perlu
                if ($category->image && file_exists(public_path($category->image))) {
                    unlink(public_path($category->image));
                }
    
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/categories'), $filename);
                $category->image = 'uploads/categories/' . $filename;
            }
    
            $category->save();
            event(new DataUpdated('category'));
            return redirect()->back()->with('success', 'Kategori berhasil diupdate!');
        }catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::with('subCategories.menus')->findOrFail($id);
    
            // 1. Hapus gambar fisik Kategori Utama
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }
    
            // 2. Loop melalui Sub-Kategori untuk hapus gambar Menu
            foreach ($category->subCategories as $sub) {
                foreach ($sub->menus as $menu) {
                    if ($menu->image && file_exists(public_path($menu->image))) {
                        unlink(public_path($menu->image));
                    }
                }
                // Hapus data Menu di sub ini
                $sub->menus()->delete();
            }
    
            // 3. Hapus semua Sub-Kategori
            $category->subCategories()->delete();
    
            // 4. Hapus Kategori Utama
            $category->delete();
    
            event(new DataUpdated('category'));
            
            return redirect()->back()->with('success', 'Kategori dan seluruh data di dalamnya berhasil dihapus!');
        }catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Gagal: ' . $e->getMessage()]);
        }
    }
}

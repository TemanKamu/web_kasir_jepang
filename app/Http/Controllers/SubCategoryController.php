<?php

namespace App\Http\Controllers;

use App\Events\DataUpdated;
use App\Models\SubCategories;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id'
        ]);

        SubCategories::create($request->all());
        event(new DataUpdated('subcategory'));
        return back()->with('success', 'Sub-Kategori berhasil ditambah!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $sub = SubCategories::findOrFail($id);
        $sub->update($request->only(['name', 'category_id']));
        event(new DataUpdated('subcategory'));
        return redirect()->back()->with('success', 'Sub Kategori berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $sub = SubCategories::findOrFail($id);

        // 1. Hapus gambar fisik dari semua menu yang ada di sub-kategori ini
        foreach ($sub->menus as $menu) {
            if ($menu->image && file_exists(public_path($menu->image))) {
                unlink(public_path($menu->image));
            }
        }

        // 2. Hapus data (Menu akan otomatis terhapus jika pakai Cascade Delete di DB, 
        // jika tidak, Laravel akan menghapusnya lewat relasi)
        $sub->menus()->delete(); 
        $sub->delete();

        event(new DataUpdated('subcategory'));
        return redirect()->back()->with('success', 'Sub Kategori dan semua menu di dalamnya berhasil dihapus!');
    }
}

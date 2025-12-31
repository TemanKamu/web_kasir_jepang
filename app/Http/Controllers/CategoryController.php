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

    public function create()
    {
        return view('categories.create');
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
            return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Gagal simpan: ' . $e->getMessage()]);
        }
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $path = $request->file('image')->store('categories', 'public');
            $data['image'] = $path;
            $data['image_url'] = Storage::url($path);
        }

        $category->update($data);
        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    }
}

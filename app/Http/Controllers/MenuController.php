<?php

namespace App\Http\Controllers;

use App\Events\MenuCreated;
use App\Events\MenuUpdated;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $products = Menu::with('category')->get();
        if (Auth::check()){
            if (Auth::id() && Auth::user() && Auth::user()->role_id == 1){
                return view('Admin.index', compact('products'));
            }else{
                return view('User.index', compact('products'));
            }
        }else{
            return redirect()->route('login');
        }
    }

    public function create()
    {
        $categories = Category::all();
        return view('', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:available,unavailable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();
        $data['count_sold'] = 0;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $data['image'] = $path;
            $data['image_url'] = Storage::url($path);
        }

        $menu = Menu::create($data);
        
        // Broadcast menu created
        broadcast(new MenuUpdated($menu, 'created'))->toOthers();
        
        return redirect()->route('menus.index')->with('success', 'Menu created successfully');
    }

    public function edit(Menu $menu)
    {
        $categories = Category::all();
        return view('menus.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:available,unavailable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $path = $request->file('image')->store('menus', 'public');
            $data['image'] = $path;
            $data['image_url'] = Storage::url($path);
        }

        $menu->update($data);
        
        // Broadcast menu updated
        broadcast(new MenuUpdated($menu->fresh(), 'updated'))->toOthers();
        
        return redirect()->route('menus.index')->with('success', 'Menu updated successfully');
    }

    public function destroy(Menu $menu)
    {
        $menuId = $menu->id;
        
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }
        $menu->delete();
        
        // Broadcast menu deleted
        broadcast(new MenuUpdated(['id' => $menuId], 'deleted'))->toOthers();
        
        return redirect()->route('menus.index')->with('success', 'Menu deleted successfully');
    }
}

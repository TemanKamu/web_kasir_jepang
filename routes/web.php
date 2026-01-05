<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SubCategoryController;
use Illuminate\Support\Facades\Auth;    

use Illuminate\Support\Facades\Route;

route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('menus.index');
    }
    return redirect()->route('login');
});
Route::get('/login', [AuthController::class, 'index']);
Route::post('/login', [AuthController::class, 'login'])->name('login'); 
// Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
Route::resource('menus', MenuController::class);
Route::get('menus', [MenuController::class, 'index'])->name('menus.index');
Route::resource('categories', CategoryController::class);
Route::resource('sub-categories', SubCategoryController::class);
Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
Route::resource('orders', OrderController::class);
Route::post('/menus/{id}/toggle-status', [MenuController::class, 'toggleStatus']);
Route::post('/orders/confirm-cart/{id}', [OrderController::class, 'confirmCart'])->name('orders.confirm-cart');
Route::resource('/users', App\Http\Controllers\UserController::class);
Route::resource('/bills', App\Http\Controllers\BillController::class);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/process-bill/{id}', [BillController::class, 'processToPos'])->name('bills.process');
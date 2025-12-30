<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Auth;    

use Illuminate\Support\Facades\Route;

route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('menus.index');
    }
    return redirect()->route('login');
});
Route::get('/login', [AuthController::class, 'index']);
Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
Route::post('/login', [AuthController::class, 'login'])->name('login'); 


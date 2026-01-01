<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['cart_group_id', 'menu_id', 'quantity', 'price'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}

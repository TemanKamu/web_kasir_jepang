<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartGroup extends Model
{
    protected $fillable = ['queue_number', 'service_type', 'total_price', 'status'];

    public function items()
    {
        // Relasi ke detail item keranjang
        return $this->hasMany(Cart::class, 'cart_group_id');
    }
}

<?php

namespace App\Models;

use App\Models\Bill;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderedMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'menu_id',
        'quantity',
        'total_price'
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
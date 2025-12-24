<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
        'price',
        'image',
        'image_url',
        'category_id',
        'count_sold',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderedMenus()
    {
        return $this->hasMany(OrderedMenu::class);
    }
}

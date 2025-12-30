<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


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

    public function subCategory()
    {
        return $this->belongsTo(SubCategories::class);
    }

    public function orderedMenus()
    {
        return $this->hasMany(OrderedMenu::class);
    }
}

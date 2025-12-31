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
    'sub_category_id', // Ini yang bener sesuai ERD
    'count_sold', 
    'status'
    ];

    public function sub_category() 
    {
        // Gunakan nama model kamu "SubCategories"
        return $this->belongsTo(SubCategories::class, 'sub_category_id');
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function orderedMenus()
    {
        return $this->hasMany(OrderedMenu::class);
    }
}

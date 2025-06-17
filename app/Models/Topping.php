<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topping extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
    ];

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_topping');
    }

    public function orderItems()
    {
        return $this->belongsToMany(OrderItem::class, 'order_item_toppings');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_id',
        'quantity',
        'price',
        'note',
        'toppings',
        'spiciness_level',
        'has_spiciness_option',
    ];

    protected $casts = [
    'has_spiciness_option' => 'boolean',
];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function toppings()
{
    return $this->belongsToMany(Topping::class, 'order_item_toppings');
}
}

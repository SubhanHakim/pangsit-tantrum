<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'image',
        'description',
        'category_id',
        'has_spiciness_option',
        'spiciness_level',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function toppings()
    {
        return $this->belongsToMany(Topping::class);
    }

    public function getSpicinessLabelAttribute()
    {
        return [
            'original' => 'Original (Tidak Pedas)',
            'mild' => 'Sedikit Pedas',
            'medium' => 'Pedas Sedang',
            'extra_pedas' => 'Extra Pedas'
        ][$this->spiciness_level] ?? 'Original';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'table_number',
        'total',
        'status',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class, 'tabel_id');
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}

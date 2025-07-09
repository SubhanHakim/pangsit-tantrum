<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'table_number',
        'total',
        'order_type',
        'status',
        'food_status',
        'preparing_at',
        'ready_at',
        'completed_at',
    ];

    protected $casts = [
        'preparing_at' => 'datetime',
        'ready_at' => 'datetime',
        'completed_at' => 'datetime',
        'total' => 'decimal:2',
    ];

    // Food Status Constants
    const FOOD_STATUS_PENDING = 'pending';
    const FOOD_STATUS_PREPARING = 'preparing';
    const FOOD_STATUS_READY = 'ready';
    const FOOD_STATUS_COMPLETED = 'completed';

    // Payment Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_SUCCESS = 'success';
    const STATUS_CHALLENGE = 'challenge';
    const STATUS_EXPIRED = 'expired';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    const ORDER_TYPE_DINE_IN = 'dine_in';
    const ORDER_TYPE_TAKEAWAY = 'takeaway';

    // Relationships
    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isDineIn()
    {
        return $this->order_type === self::ORDER_TYPE_DINE_IN;
    }

    public function isTakeaway()
    {
        return $this->order_type === self::ORDER_TYPE_TAKEAWAY;
    }

    // Food Status Helper Methods
    public function isPending()
    {
        return $this->food_status === self::FOOD_STATUS_PENDING;
    }

    public function isPreparing()
    {
        return $this->food_status === self::FOOD_STATUS_PREPARING;
    }

    public function isReady()
    {
        return $this->food_status === self::FOOD_STATUS_READY;
    }

    public function isCompleted()
    {
        return $this->food_status === self::FOOD_STATUS_COMPLETED;
    }

    // Payment Status Helper Methods
    public function isPaid()
    {
        return in_array($this->status, [self::STATUS_PAID, self::STATUS_SUCCESS]);
    }

    public function isPaymentPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPaymentFailed()
    {
        return in_array($this->status, [self::STATUS_FAILED, self::STATUS_EXPIRED, self::STATUS_CANCELLED]);
    }

    // Accessors for Food Status
    public function getFoodStatusLabelAttribute()
    {
        return match ($this->food_status) {
            self::FOOD_STATUS_PENDING => 'Menunggu Konfirmasi',
            self::FOOD_STATUS_PREPARING => 'Sedang Dipersiapkan',
            self::FOOD_STATUS_READY => 'Siap Disajikan',
            self::FOOD_STATUS_COMPLETED => 'Selesai',
            default => 'Unknown'
        };
    }

    public function getFoodStatusColorAttribute()
    {
        return match ($this->food_status) {
            self::FOOD_STATUS_PENDING => 'text-yellow-600',
            self::FOOD_STATUS_PREPARING => 'text-orange-600',
            self::FOOD_STATUS_READY => 'text-blue-600',
            self::FOOD_STATUS_COMPLETED => 'text-green-600',
            default => 'text-gray-600'
        };
    }

    public function getFoodStatusIconAttribute()
    {
        return match ($this->food_status) {
            self::FOOD_STATUS_PENDING => '⏳',
            self::FOOD_STATUS_PREPARING => '👨‍🍳',
            self::FOOD_STATUS_READY => '🔔',
            self::FOOD_STATUS_COMPLETED => '✅',
            default => '❓'
        };
    }

    public function getFoodStatusBadgeColorAttribute()
    {
        return match ($this->food_status) {
            self::FOOD_STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::FOOD_STATUS_PREPARING => 'bg-orange-100 text-orange-800',
            self::FOOD_STATUS_READY => 'bg-blue-100 text-blue-800',
            self::FOOD_STATUS_COMPLETED => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    // Accessors for Payment Status
    public function getPaymentStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_PAID => 'Sudah Dibayar',
            self::STATUS_SUCCESS => 'Berhasil',
            self::STATUS_CHALLENGE => 'Verifikasi',
            self::STATUS_EXPIRED => 'Kadaluarsa',
            self::STATUS_FAILED => 'Gagal',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => 'Unknown'
        };
    }

    public function getPaymentStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'text-yellow-600',
            self::STATUS_PAID, self::STATUS_SUCCESS => 'text-green-600',
            self::STATUS_CHALLENGE => 'text-orange-600',
            self::STATUS_EXPIRED => 'text-gray-600',
            self::STATUS_FAILED, self::STATUS_CANCELLED => 'text-red-600',
            default => 'text-gray-600'
        };
    }

    // Methods untuk update food status
    public function markAsPreparing()
    {
        $this->update([
            'food_status' => self::FOOD_STATUS_PREPARING,
            'preparing_at' => now()
        ]);

        return $this;
    }

    public function markAsReady()
    {
        $this->update([
            'food_status' => self::FOOD_STATUS_READY,
            'ready_at' => now()
        ]);

        return $this;
    }

    public function markAsCompleted()
    {
        $this->update([
            'food_status' => self::FOOD_STATUS_COMPLETED,
            'completed_at' => now()
        ]);

        return $this;
    }

    // Calculate total dari items
    public function calculateTotal()
    {
        $total = 0;

        foreach ($this->items as $item) {
            $itemTotal = $item->menu->price * $item->quantity;

            // Add toppings cost
            if ($item->toppings) {
                $toppings = is_string($item->toppings) ? json_decode($item->toppings, true) : $item->toppings;
                if (is_array($toppings)) {
                    foreach ($toppings as $topping) {
                        $itemTotal += ($topping['price'] ?? 0) * $item->quantity;
                    }
                }
            }

            $total += $itemTotal;
        }

        return $total;
    }

    // Update total
    public function updateTotal()
    {
        $this->update(['total' => $this->calculateTotal()]);
        return $this;
    }

    // Get estimated cooking time (dalam menit)
    public function getEstimatedCookingTimeAttribute()
    {
        $totalTime = 0;

        foreach ($this->items as $item) {
            // Asumsi setiap menu memiliki cooking_time, atau default 15 menit
            $cookingTime = $item->menu->cooking_time ?? 15;
            $totalTime = max($totalTime, $cookingTime); // Ambil yang paling lama
        }

        return $totalTime;
    }

    // Get estimated ready time - PERBAIKAN DI SINI
    public function getEstimatedReadyTimeAttribute()
    {
        if ($this->preparing_at) {
            // Gunakan addMinutes() untuk Carbon instance
            return $this->preparing_at->copy()->addMinutes($this->estimated_cooking_time);
        }

        return null;
    }

    // Check if order is late
    public function isLate()
    {
        if (!$this->estimated_ready_time || $this->isCompleted()) {
            return false;
        }

        return now() > $this->estimated_ready_time;
    }

    // Scopes
    public function scopePendingFood($query)
    {
        return $query->where('food_status', self::FOOD_STATUS_PENDING);
    }

    public function scopePreparing($query)
    {
        return $query->where('food_status', self::FOOD_STATUS_PREPARING);
    }

    public function scopeReady($query)
    {
        return $query->where('food_status', self::FOOD_STATUS_READY);
    }

    public function scopeCompletedFood($query)
    {
        return $query->where('food_status', self::FOOD_STATUS_COMPLETED);
    }

    public function scopePaid($query)
    {
        return $query->whereIn('status', [self::STATUS_PAID, self::STATUS_SUCCESS]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    // Boot method untuk auto-generate order_id
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_id)) {
                $order->order_id = 'ORD-' . now()->format('Ymd') . '-' . str_pad(
                    Order::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }

            if (empty($order->food_status)) {
                $order->food_status = self::FOOD_STATUS_PENDING;
            }
        });
    }

    // Format methods
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    // Get order summary for display
    public function getSummaryAttribute()
    {
        $itemCount = $this->items->count();
        $firstItem = $this->items->first();

        if ($itemCount === 1) {
            return $firstItem->menu->name;
        } elseif ($itemCount === 2) {
            return $firstItem->menu->name . ' +1 item lainnya';
        } else {
            return $firstItem->menu->name . ' +' . ($itemCount - 1) . ' item lainnya';
        }
    }
}

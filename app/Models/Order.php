<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'delivery_method',
        'recipient_name',
        'recipient_phone',
        'shipping_address',
        'notes',
        'payment_method',
        'payment_status',
        'payment_proof',
        'payment_note',
        'status',
        'subtotal',
        'shipping_cost',
        'total',
        'ordered_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total' => 'decimal:2',
            'ordered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        return $this->payment_proof ? Storage::url($this->payment_proof) : null;
    }

    public function getDeliveryLabelAttribute(): string
    {
        return $this->delivery_method === 'pickup' ? 'Ambil sendiri' : 'Dikirimkan';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return $this->payment_method === 'qris' ? 'QRIS' : 'Transfer bank';
    }
}

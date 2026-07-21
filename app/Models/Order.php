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

        'destination_id',
        'destination_label',
        'destination_province',
        'destination_city',
        'destination_district',
        'destination_subdistrict',
        'destination_postal_code',

        'courier_code',
        'courier_name',
        'courier_service',
        'courier_description',
        'shipping_etd',
        'total_weight_grams',

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
            'destination_id' => 'integer',
            'total_weight_grams' => 'integer',

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

    public function getCourierLabelAttribute(): ?string
    {
        if ($this->delivery_method === 'pickup') {
            return 'Ambil sendiri';
        }

        if (! $this->courier_code) {
            return null;
        }

        return trim(
            strtoupper($this->courier_code)
            . ' '
            . $this->courier_service
        );
    }
    
    public function getFormattedWeightAttribute(): string
    {
        $weight = (int) $this->total_weight_grams;

        if ($weight <= 0) {
            return '-';
        }

        if ($weight >= 1000) {
            $kilograms = number_format(
                $weight / 1000,
                2,
                ',',
                '.'
            );

            $kilograms = rtrim(
                rtrim($kilograms, '0'),
                ','
            );

            return $kilograms . ' kg';
        }

        return number_format(
            $weight,
            0,
            ',',
            '.'
        ) . ' gram';
    }

    public function getShippingServiceLabelAttribute(): string
    {
        if ($this->delivery_method === 'pickup') {
            return 'Ambil sendiri';
        }

        if (! $this->courier_code) {
            return '-';
        }

        return trim(
            strtoupper($this->courier_code)
            . ' '
            . $this->courier_service
        );
    }

    public function getDestinationAddressAttribute(): string
    {
        if ($this->destination_label) {
            return $this->destination_label;
        }

        return collect([
            $this->destination_subdistrict,
            $this->destination_district,
            $this->destination_city,
            $this->destination_province,
            $this->destination_postal_code,
        ])
            ->filter()
            ->implode(', ');
    }
}

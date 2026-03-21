<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\ShippingMethod;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'status', 'payment_method', 'shipping_address', 'billing_address', 'phone', 'notes',
        'shipping_method', 'subtotal', 'discount', 'shipping_cost', 'coupon_code', 'total_cost',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_method' => PaymentMethod::class,
            'shipping_method' => ShippingMethod::class,
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->oldest()->oldest('id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function number(): string
    {
        return '#'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function itemCount(): int
    {
        return (int) $this->orderDetails->sum('quantity');
    }

    public function isCancellableByCustomer(): bool
    {
        return $this->status === OrderStatus::WaitingForPayment;
    }

    #[Scope]
    protected function countingTowardsRevenue(Builder $query): void
    {
        $query->whereIn('status', array_map(fn (OrderStatus $status) => $status->value, OrderStatus::revenue()));
    }
}

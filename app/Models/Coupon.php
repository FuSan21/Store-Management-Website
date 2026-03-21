<?php

namespace App\Models;

use App\Enums\CouponType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'description', 'type', 'value', 'min_subtotal', 'max_uses', 'used_count', 'starts_at', 'expires_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => CouponType::class,
            'value' => 'decimal:2',
            'min_subtotal' => 'decimal:2',
            'max_uses' => 'integer',
            'used_count' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected function code(): Attribute
    {
        return Attribute::set(fn (string $value) => strtoupper(trim($value)));
    }

    public static function findByCode(?string $code): ?self
    {
        return filled($code) ? static::where('code', strtoupper(trim($code)))->first() : null;
    }

    /**
     * Why the coupon cannot be used on this subtotal, or null when it can.
     */
    public function rejectionReason(float $subtotal): ?string
    {
        return match (true) {
            ! $this->is_active => 'This code is no longer active.',
            $this->starts_at?->isFuture() => 'This code is not active yet.',
            $this->expires_at?->isPast() => 'This code has expired.',
            $this->max_uses !== null && $this->used_count >= $this->max_uses => 'This code has been fully redeemed.',
            $this->min_subtotal !== null && $subtotal < (float) $this->min_subtotal => 'Spend at least '.money($this->min_subtotal).' to use this code.',
            default => null,
        };
    }

    public function discountFor(float $subtotal): float
    {
        if ($this->rejectionReason($subtotal) !== null) {
            return 0.0;
        }

        $discount = $this->type === CouponType::Percent
            ? $subtotal * ((float) $this->value / 100)
            : (float) $this->value;

        return round(min($discount, $subtotal), 2);
    }

    public function summary(): string
    {
        return $this->type === CouponType::Percent
            ? rtrim(rtrim(number_format((float) $this->value, 2), '0'), '.').'% off'
            : money($this->value).' off';
    }
}

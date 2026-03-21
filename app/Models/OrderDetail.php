<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'bmuk_no', 'quantity', 'unit_price'];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'bmuk_no', 'bmuk_no')->withTrashed();
    }

    /**
     * The price the line sold at, falling back to the live price for rows saved before snapshots existed.
     */
    public function price(): float
    {
        return (float) ($this->unit_price ?? $this->product?->price ?? 0);
    }

    public function lineTotal(): float
    {
        return $this->price() * $this->quantity;
    }
}

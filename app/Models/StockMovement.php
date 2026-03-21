<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    public const REASON_SALE = 'sale';

    public const REASON_RESTOCK = 'restock';

    public const REASON_CANCELLATION = 'cancellation';

    public const REASON_ADJUSTMENT = 'adjustment';

    protected $fillable = ['bmuk_no', 'change', 'stock_after', 'reason', 'note', 'order_id', 'user_id'];

    protected function casts(): array
    {
        return [
            'change' => 'integer',
            'stock_after' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'bmuk_no', 'bmuk_no')->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

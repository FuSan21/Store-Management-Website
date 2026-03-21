<?php

namespace App\Actions;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdjustStock
{
    /**
     * Add or remove units by hand (a delivery, a count correction, damage).
     */
    public function handle(Product $product, int $change, string $reason, ?string $note, User $by): Product
    {
        DB::transaction(function () use ($product, $change, $reason, $note, $by) {
            $product = Product::whereKey($product->bmuk_no)->lockForUpdate()->firstOrFail();

            if ($product->stock + $change < 0) {
                throw ValidationException::withMessages([
                    'change' => "Only {$product->stock} in stock, so at most {$product->stock} can be removed.",
                ]);
            }

            $product->increment('stock', $change);

            StockMovement::create([
                'bmuk_no' => $product->bmuk_no,
                'change' => $change,
                'stock_after' => $product->stock,
                'reason' => $reason,
                'note' => $note,
                'user_id' => $by->id,
            ]);
        });

        return $product->refresh();
    }
}

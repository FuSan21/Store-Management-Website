<?php

namespace App\Support\Cart;

use App\Models\Product;

final readonly class CartLine
{
    public function __construct(
        public Product $product,
        public int $quantity,
    ) {}

    public function total(): float
    {
        return (float) $this->product->price * $this->quantity;
    }

    /**
     * True when the shelf no longer holds as many as the cart asks for.
     */
    public function exceedsStock(): bool
    {
        return $this->quantity > $this->product->stock;
    }
}

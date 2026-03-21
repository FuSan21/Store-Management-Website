<?php

if (! function_exists('money')) {
    /**
     * Format an amount in the store currency: "৳1,250" or "৳1,250.50".
     */
    function money(float|int|string|null $amount): string
    {
        $amount = (float) $amount;
        $decimals = fmod($amount, 1.0) === 0.0 ? 0 : 2;

        return config('store.currency').number_format($amount, $decimals);
    }
}

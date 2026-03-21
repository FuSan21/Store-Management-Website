<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Storefront
    |--------------------------------------------------------------------------
    */

    'name' => env('STORE_NAME', 'Vehicle Accessory Shop'),

    'tagline' => 'Oil, air, fuel and cabin filters for the cars on Bangladesh roads',

    'currency' => env('STORE_CURRENCY', '৳'),

    'contact' => [
        'email' => env('STORE_CONTACT_EMAIL', 'support@vehicle-accessory-shop.example'),
        'area' => 'Tejgaon Industrial Area, Dhaka',
        'hours' => 'Saturday to Thursday, 10:00 to 19:00',
    ],

    /*
    |--------------------------------------------------------------------------
    | Checkout
    |--------------------------------------------------------------------------
    |
    | Courier rates are flat per order. Orders whose subtotal (after discount)
    | reaches the free-shipping threshold ship free with any courier.
    |
    */

    'shipping' => [
        'Steadfast' => ['label' => 'Steadfast Courier', 'cost' => 80, 'eta' => '1-2 days inside Dhaka'],
        'Sundarban' => ['label' => 'Sundarban Courier', 'cost' => 120, 'eta' => '2-4 days nationwide'],
        'eCourier' => ['label' => 'eCourier', 'cost' => 100, 'eta' => '2-3 days nationwide'],
    ],

    'free_shipping_threshold' => (float) env('STORE_FREE_SHIPPING_FROM', 5000),

    'cart_max_quantity' => 50,

    'recently_viewed_limit' => 8,

];

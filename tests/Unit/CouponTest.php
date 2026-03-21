<?php

use App\Enums\CouponType;
use App\Enums\OrderStatus;
use App\Models\Coupon;

test('percentage coupons take a share of the subtotal', function () {
    $coupon = Coupon::factory()->make(['type' => CouponType::Percent, 'value' => 15]);

    expect($coupon->discountFor(2000))->toBe(300.0);
});

test('fixed coupons never discount more than the subtotal', function () {
    $coupon = Coupon::factory()->make(['type' => CouponType::Fixed, 'value' => 500]);

    expect($coupon->discountFor(300))->toBe(300.0);
});

test('coupons outside their window give nothing', function () {
    $future = Coupon::factory()->make(['starts_at' => now()->addDay()]);
    $inactive = Coupon::factory()->make(['is_active' => false]);

    expect($future->discountFor(1000))->toBe(0.0)
        ->and($inactive->rejectionReason(1000))->toBe('This code is no longer active.');
});

test('codes are stored upper case', function () {
    expect(Coupon::factory()->make(['code' => ' welcome10 '])->code)->toBe('WELCOME10');
});

test('order statuses only move forward along allowed paths', function () {
    expect(OrderStatus::WaitingForPayment->canTransitionTo(OrderStatus::Processing))->toBeTrue()
        ->and(OrderStatus::WaitingForPayment->canTransitionTo(OrderStatus::Completed))->toBeFalse()
        ->and(OrderStatus::Completed->transitions())->toBe([])
        ->and(OrderStatus::Cancelled->transitions())->toBe([]);
});

test('money is formatted in taka', function () {
    expect(money(1250))->toBe('৳1,250')
        ->and(money(1250.5))->toBe('৳1,250.50');
});

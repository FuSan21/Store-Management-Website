<?php

namespace Database\Seeders;

use App\Enums\CouponType;
use App\Enums\OrderStatus;
use App\Enums\ShippingMethod;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Wishlist;
use Database\Factories\ReviewFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Demo data for a working store: sign in as admin@example.com or
     * customer@example.com, both with the password "password".
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Store Admin', 'email' => 'admin@example.com']);
        $demo = User::factory()->create(['name' => 'Rahim Chowdhury', 'email' => 'customer@example.com']);
        $customers = User::factory(24)->create()->push($demo);

        $customers->each(fn (User $user) => Address::factory()->for($user)->create(['recipient' => $user->name]));

        $products = Product::factory(56)->create()
            ->merge(Product::factory(4)->featured()->create(['stock' => 40]));

        // A few shelves running low so the inventory alerts have something to show.
        $products->random(5)->each(fn (Product $product) => $product->update(['stock' => fake()->numberBetween(1, 4)]));
        $products->random(2)->each(fn (Product $product) => $product->update(['stock' => 0]));

        $products->each(fn (Product $product) => StockMovement::create([
            'bmuk_no' => $product->bmuk_no,
            'change' => $product->stock,
            'stock_after' => $product->stock,
            'reason' => StockMovement::REASON_RESTOCK,
            'note' => 'Opening stock',
            'user_id' => $admin->id,
            'created_at' => now()->subDays(95),
        ]));

        $this->seedOrders($customers, $products, $admin);

        Coupon::create(['code' => 'WELCOME10', 'description' => '10% off a first order over ৳1,000', 'type' => CouponType::Percent, 'value' => 10, 'min_subtotal' => 1000]);
        Coupon::create(['code' => 'SERVICE500', 'description' => '৳500 off a full service kit', 'type' => CouponType::Fixed, 'value' => 500, 'min_subtotal' => 4000, 'max_uses' => 100]);
        Coupon::create(['code' => 'EID2025', 'description' => 'Last Eid sale', 'type' => CouponType::Percent, 'value' => 15, 'expires_at' => now()->subMonths(4)]);

        $products->where('stock', '>', 0)->random(4)->each(fn (Product $product) => Wishlist::create(['user_id' => $demo->id, 'bmuk_no' => $product->bmuk_no]));
        $products->where('stock', '>', 2)->random(2)->each(fn (Product $product) => Cart::create(['user_id' => $demo->id, 'bmuk_no' => $product->bmuk_no, 'quantity' => 1]));
    }

    private function seedOrders($customers, $products, User $admin): void
    {
        $weights = [
            OrderStatus::Completed->value => 50,
            OrderStatus::Shipped->value => 10,
            OrderStatus::Processing->value => 14,
            OrderStatus::WaitingForPayment->value => 12,
            OrderStatus::Cancelled->value => 9,
            OrderStatus::Suspended->value => 5,
        ];

        foreach (range(1, 140) as $i) {
            $customer = $customers->random();
            $placedAt = Carbon::now()->subDays(fake()->biasedNumberBetween(0, 90, fn ($x) => 1 - sqrt($x)))->setTime(fake()->numberBetween(9, 22), fake()->numberBetween(0, 59));
            $status = OrderStatus::from($this->weighted($weights));
            $shipping = fake()->randomElement(ShippingMethod::cases());
            $lines = $products->random(fake()->numberBetween(1, 4));
            $quantities = $lines->mapWithKeys(fn (Product $product) => [$product->bmuk_no => fake()->numberBetween(1, 3)]);
            $subtotal = $lines->sum(fn (Product $product) => $product->price * $quantities[$product->bmuk_no]);
            $shippingCost = $subtotal >= config('store.free_shipping_threshold') ? 0 : $shipping->cost();

            $order = Order::factory()->for($customer)->create([
                'status' => $status,
                'shipping_method' => $shipping,
                'shipping_address' => $customer->addresses->first()?->oneLine() ?? 'Dhanmondi, Dhaka',
                'billing_address' => $customer->addresses->first()?->oneLine() ?? 'Dhanmondi, Dhaka',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total_cost' => $subtotal + $shippingCost,
                'created_at' => $placedAt,
                'updated_at' => $placedAt,
            ]);

            foreach ($lines as $product) {
                $order->orderDetails()->create([
                    'bmuk_no' => $product->bmuk_no,
                    'quantity' => $quantities[$product->bmuk_no],
                    'unit_price' => $product->price,
                ]);
            }

            $this->seedTimeline($order, $status, $placedAt, $admin);

            if ($status === OrderStatus::Completed) {
                foreach ($lines as $product) {
                    if (fake()->boolean(55)) {
                        Review::create([
                            'user_id' => $customer->id,
                            'product_id' => $product->bmuk_no,
                            'order_id' => $order->id,
                            'rating' => fake()->randomElement([5, 5, 5, 4, 4, 4, 3, 2]),
                            'comment' => fake()->randomElement(ReviewFactory::COMMENTS),
                            'created_at' => $placedAt->copy()->addDays(6),
                        ]);
                    }
                }
            }
        }
    }

    private function seedTimeline(Order $order, OrderStatus $final, Carbon $placedAt, User $admin): void
    {
        $path = match ($final) {
            OrderStatus::WaitingForPayment => [OrderStatus::WaitingForPayment],
            OrderStatus::Processing => [OrderStatus::WaitingForPayment, OrderStatus::Processing],
            OrderStatus::Shipped => [OrderStatus::WaitingForPayment, OrderStatus::Processing, OrderStatus::Shipped],
            OrderStatus::Completed => [OrderStatus::WaitingForPayment, OrderStatus::Processing, OrderStatus::Shipped, OrderStatus::Completed],
            OrderStatus::Cancelled => [OrderStatus::WaitingForPayment, OrderStatus::Cancelled],
            OrderStatus::Suspended => [OrderStatus::WaitingForPayment, OrderStatus::Suspended],
        };

        foreach ($path as $step => $status) {
            $at = $placedAt->copy()->addHours($step * fake()->numberBetween(10, 30));

            $order->statusHistories()->create([
                'status' => $status,
                'note' => $step === 0 ? 'Order placed' : null,
                'changed_by' => $step === 0 ? $order->user_id : $admin->id,
                'created_at' => $at,
                'updated_at' => $at,
            ]);
        }
    }

    /**
     * @param  array<string, int>  $weights
     */
    private function weighted(array $weights): string
    {
        $pick = fake()->numberBetween(1, array_sum($weights));

        foreach ($weights as $value => $weight) {
            if (($pick -= $weight) <= 0) {
                return $value;
            }
        }

        return array_key_first($weights);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // On Postgres, Laravel 8 enums are varchar columns guarded by a check constraint,
        // which would keep rejecting new statuses after the type change below.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('alter table orders drop constraint if exists orders_status_check');
        }

        Schema::table('orders', function (Blueprint $table) {
            // A plain string lets new statuses (Shipped) exist without enum rewrites.
            $table->string('status', 30)->default('Waiting for Payment')->change();
            $table->decimal('total_cost', 10, 2)->change();
            $table->string('shipping_address', 255)->change();
            $table->string('billing_address', 255)->change();
            $table->decimal('subtotal', 10, 2)->default(0)->after('shipping_method');
            $table->decimal('discount', 10, 2)->default(0)->after('subtotal');
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('discount');
            $table->string('coupon_code', 40)->nullable()->after('shipping_cost');
            $table->string('phone', 20)->nullable()->after('billing_address');
            $table->text('notes')->nullable()->after('phone');

            $table->index('status');
            $table->index('created_at');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->nullable()->after('quantity');
        });

        // Legacy orders stored only a grand total; treat it as the subtotal.
        DB::table('orders')->update(['subtotal' => DB::raw('total_cost')]);

        // Snapshot the price each legacy line was sold at from the current product price.
        DB::table('order_details')
            ->whereNull('unit_price')
            ->update([
                'unit_price' => DB::raw('(select price from products where products.bmuk_no = order_details.bmuk_no)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('unit_price');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropColumn(['subtotal', 'discount', 'shipping_cost', 'coupon_code', 'phone', 'notes']);
        });
    }
};

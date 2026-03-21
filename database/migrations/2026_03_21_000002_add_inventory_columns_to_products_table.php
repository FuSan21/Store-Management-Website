<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
            $table->json('gallery')->nullable()->after('image');
            $table->text('description')->nullable()->after('gallery');
            $table->unsignedInteger('stock')->default(0)->after('price');
            $table->unsignedSmallInteger('low_stock_threshold')->default(5)->after('stock');
            $table->boolean('is_featured')->default(false)->after('low_stock_threshold');
            $table->softDeletes();

            $table->index('car_brand');
            $table->index(['car_brand', 'model']);
            $table->index('collection');
        });

        // Products listed before inventory tracking existed start with a workable stock level.
        DB::table('products')->update(['stock' => 25]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['car_brand']);
            $table->dropIndex(['car_brand', 'model']);
            $table->dropIndex(['collection']);
            $table->dropSoftDeletes();
            $table->dropColumn(['description', 'gallery', 'stock', 'low_stock_threshold', 'is_featured']);
        });
    }
};

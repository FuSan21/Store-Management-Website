<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30);
            $table->string('note')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Give every existing order a starting point on its timeline.
        DB::table('orders')->orderBy('id')->chunkById(500, function ($orders) {
            DB::table('order_status_histories')->insert($orders->map(fn ($order) => [
                'order_id' => $order->id,
                'status' => $order->status,
                'note' => null,
                'changed_by' => null,
                'created_at' => $order->updated_at ?? $order->created_at,
                'updated_at' => $order->updated_at ?? $order->created_at,
            ])->all());
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};

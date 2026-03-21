<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label', 40);
            $table->string('recipient', 100);
            $table->string('phone', 20)->nullable();
            $table->string('line', 160);
            $table->string('city', 60);
            $table->boolean('is_default_shipping')->default(false);
            $table->boolean('is_default_billing')->default(false);
            $table->timestamps();
        });

        // Move each user's single shipping and billing strings into the address book.
        $now = now();

        DB::table('users')->orderBy('id')->chunkById(500, function ($users) use ($now) {
            $rows = [];

            foreach ($users as $user) {
                $shipping = trim((string) $user->shipping_address);
                $billing = trim((string) $user->billing_address);

                if ($shipping !== '') {
                    $rows[] = [
                        'user_id' => $user->id,
                        'label' => 'Shipping',
                        'recipient' => $user->name,
                        'phone' => null,
                        'line' => mb_substr($shipping, 0, 160),
                        'city' => 'Dhaka',
                        'is_default_shipping' => true,
                        'is_default_billing' => $billing === $shipping,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if ($billing !== '' && $billing !== $shipping) {
                    $rows[] = [
                        'user_id' => $user->id,
                        'label' => 'Billing',
                        'recipient' => $user->name,
                        'phone' => null,
                        'line' => mb_substr($billing, 0, 160),
                        'city' => 'Dhaka',
                        'is_default_shipping' => false,
                        'is_default_billing' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if ($rows) {
                DB::table('addresses')->insert($rows);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};

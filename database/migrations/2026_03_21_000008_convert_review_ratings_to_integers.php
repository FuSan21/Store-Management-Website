<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Laravel 8 stored ratings as enum('1'..'5'). On Postgres that is a varchar,
     * which avg() rejects, so ratings become a small integer everywhere.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('alter table reviews drop constraint if exists reviews_rating_check');
            DB::statement('alter table reviews alter column rating type smallint using rating::smallint');

            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->change();
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('rating', 1)->change();
        });
    }
};

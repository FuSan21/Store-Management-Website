<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('bmuk_no', 50);
            $table->timestamps();
            $table->string('euro_id', 50)->unique();
            $table->string('oem_id', 50)->unique();
            $table->string('image')->default('https://www.shutterstock.com/image-vector/demo-cursor-icon-presentation-billboard-600w-318562925.jpg');
            $table->string('collection', 20);
            $table->string('filter_type', 20);
            $table->string('car_brand', 20);
            $table->string('model', 20);
            $table->string('specification')->default('{"specifications":[ {"engine":"1.0L", "fuelType":"Diesel", "year":"2013-2022"}, {"engine":"1.5L", "fuelType":"Diesel", "year":"2013-2022"}, {"engine":"2.0L", "fuelType":"Diesel", "year":"2013-2022"}, ] }');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}

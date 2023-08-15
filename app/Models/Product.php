<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = "products";
    protected $primarykey = "bmuk_no";

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'bmuk_no');
    }
}

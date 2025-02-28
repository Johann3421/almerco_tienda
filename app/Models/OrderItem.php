<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'id',
    ];

    public function active(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? true : false,
        );
    }

    public function getProduct()
    {
        // Quiero solo algunos datos del producto
        return $this->hasOne(Product::class, 'id', 'product_id')->select('id', 'name', 'price', 'stock');
    }
}

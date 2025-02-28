<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilterItem extends Model
{
    use HasFactory;

    public function filter()
    {
        return $this->belongsTo(Filter::class);
    }
    public function filterItems()
    {
        return $this->belongsToMany(Product::class, 'filter_items_products');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'filter_items_products', 'filter_item_id', 'product_id');
    }

    public function active(): Attribute
    {
        return new Attribute(function ($value) {
            return $value ? true : false;
        });
    }
}

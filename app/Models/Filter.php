<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Filter extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->belongsToMany(Product::class, 'filter_items_products', 'filter_item_id', 'product_id');
    }

    public function filterItems()
    {
        return $this->hasMany(FilterItem::class);
    }

    public function active(): Attribute
    {
        return new Attribute(function ($value) {
            return $value ? true : false;
        });
    }

    public function name(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Str::upper($value),
            set: fn ($value) => Str::upper($value),
        );
    }
}

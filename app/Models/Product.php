<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'presentation',
        'price',
        'previous_price',
        'stock',
        'hdmi_ports',
        'token',
    ];

    public function onSale(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? true : false,
        );
    }
    public function onPromotion(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? true : false,
        );
    }

    public function active(): Attribute
    {
        return new Attribute(function ($value) {
            return $value ? true : false;
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subgroups()
    {
        return $this->belongsToMany(Subgroup::class, 'products_subgroups')->select('subgroups.id', 'subgroups.name', 'subgroups.slug');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->select('id', 'product_id', 'file_path', 'file')->orderBy('id', 'asc');
    }

    public function filters()
    {
        return $this->belongsToMany(Filter::class, 'filter_items_products', 'product_id', 'filter_item_id');
    }

    public function filters_items()
    {
        return $this->belongsToMany(FilterItem::class, 'filter_items_products', 'product_id', 'filter_item_id')->select('filter_items.id', 'filter_items.name');
    }
    public function filterItems()
    {
        return $this->belongsToMany(FilterItem::class, 'filter_items_products', 'product_id', 'filter_item_id')->with('filter');
    }

    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class)->select('id', 'product_id', 'key', 'value');
    }
}

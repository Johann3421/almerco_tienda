<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subgroup extends Model
{
    use HasFactory;
    protected $table = 'subgroups';
    public function featured(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? true : false,
        );
    }
    public function active(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? true : false,
        );
    }

    public function slug(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Str::slug($value),
            set: fn ($value) => Str::slug($value),
        );
    }

    public function name(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Str::upper($value),
            set: fn ($value) => Str::upper($value),
        );
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'products_subgroups');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id')->with('category');
    }

    public function subgroups()
    {
        return $this->belongsTo(Group::class)->select('id', 'name', 'category_id');
    }
    public function subgroupsToProducts()
    {
        return $this->belongsTo(Subgroup::class, 'products_subgroups');
    }
}

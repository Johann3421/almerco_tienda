<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
    ];
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

    public function description(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Str::upper($value),
        );
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function groups()
{
    return $this->hasMany(Group::class, 'category_id')->with('subgroups');
}
 

    public function subgroups()
    {
        return $this->hasManyThrough(Subgroup::class, Group::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}

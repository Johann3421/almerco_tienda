<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

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
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subgroups()
    {
        return $this->hasMany(Subgroup::class, 'group_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value'
    ];


    public function active(): Attribute
    {
        return new Attribute(function ($value) {
            return $value ? true : false;
        });
    }
    public static function getValue($key)
    {
        return self::where('key', $key)->value('value');
    }
}

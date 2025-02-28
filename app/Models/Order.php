<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'order_code',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_document_number',
        'customer_document_type',
        'order_status',
        'order_code_otp',
        'order_total',
        'observation',
        'active',
    ];

    public function active(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ? true : false,
        );
    }

    public function getProducts()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function getProductsCount()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id')->count();
    }

    public function getProductsTotal()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id')->sum('total');
    }

    public function getProductsTotal2()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id')->sum('total_price');
    }

}

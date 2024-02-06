<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = ['order_id','product_id','product_name','quantity','unit_price','weight','discount','is_reserved','actual_reserved_quantity','not_reserved_reason','variant_id'];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id','id')->with('images');
    }

}

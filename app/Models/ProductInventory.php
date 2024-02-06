<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    use HasFactory;

    protected $fillable =['product_id','master_quantity','reserved_quantity','stock_out_quantity','is_approved','entered_by','is_reserved','variant_id'];

    public function product(){
        return $this->belongsTo(Product::class,'product_id','id');
    }

    
}

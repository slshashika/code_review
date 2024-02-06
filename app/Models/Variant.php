<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Variant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','variant_name','description','unit_price','packing_cost','selling_price','weight','status'];

    public function inventory(){
        return $this->hasOne(ProductInventory::class);
    }

    public function inventoryHistories(){
        return $this->hasMany(ProductInventoryHistory::class)->orderBy('id','desc');
    }

 
}

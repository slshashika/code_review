<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCharge extends Model
{
    use HasFactory;

    protected  $fillable = ['shipping_charges_type','weight_margin','weight_margin_cost','additional_weight','additional_weight_cost','is_active'];

    public static function loadShippingChargeMethods(){
       // only active methods
       return ShippingCharge::where('is_active',1)->first();

    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $table = "coupons";
    protected $fillable = ['coupon_code','coupon_type','coupon_value','coupon_name','assigned','customer_id','status','expiry_date'];

    public static function getCouponForFilters($searchKey){

        return Coupon::where('coupon_name','like','%'.$searchKey.'%')->orderBy('id','desc')
        ->paginate(env("RECORDS_PER_PAGE"));
    }






}

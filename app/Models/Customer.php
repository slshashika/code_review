<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = ['order_id','first_name','last_name','email','phone','address','user_id','status'];

    public function billingAddress(){
        return $this->hasOne(Address::class)->where('type',0)->where('active_status',1);
    }

    public function shippingAddress(){
        return $this->hasOne(Address::class)->where('type',1)->where('active_status',1);
    }

    public function billingAddresses(){
        return $this->hasMany(CustomerAddress::class)->where('type',0);
    }

    public function wishlist(){
        return $this->hasMany(Wishlist::class)->with('product');
    }

    public function shippingAddresses(){
        return $this->hasMany(CustomerAddress::class)->where('type',1);
    }

    public function orders(){
        return $this->hasMany(Order::class)->with('billingAddress','shippingAddress','orderItems','customer')->orderBy('id','desc');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public static function getCustomersForFilters($searchKey){
        return Customer::with('shippingAddresses','billingAddresses')
        ->where('first_name','like','%'.$searchKey.'%')
        ->orWhere('last_name','like','%'.$searchKey.'%')
        ->orWhere('email','like','%'.$searchKey.'%')
        ->orWhere('phone','like','%'.$searchKey.'%')
        ->paginate(env("RECORDS_PER_PAGE"));
    }

}

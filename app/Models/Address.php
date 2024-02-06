<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    
    protected $fillable = ['order_id','customer_id','type','first_name','last_name','email','company','address_line1','address_line2','country','city','zip','phone','user_id','active_status','state'];

    const BILLING = 0;
    const SHIPPING = 1;

    public function customer(){
        return $this->hasMany(Customer::class);
    }

    public function order(){
        return $this->hasOne(Order::class);
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}

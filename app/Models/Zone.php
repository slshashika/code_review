<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;
    
    protected $fillable =['zone_name','zone_description','shipping_cost','weight_margin','minimum_cost','zip_code'];

    
}



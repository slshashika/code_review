<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkedProduct extends Model
{
    use HasFactory;

    protected $fillable = ['parent_product_id','linked_product_id'];
}

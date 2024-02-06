<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;


    protected $fillable = ['email','is_active'];

    public static function loadAllSubscribers(){
        return Subscriber::where('is_active',1)->paginate(env("RECORDS_PER_PAGE"));;
    }
}

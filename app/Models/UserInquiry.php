<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInquiry extends Model
{
    use HasFactory;
    protected $fillable=[
        'name','email' ,'phone' , 'message','status'
    ];

    public static function getInquiriesForFilters($searchKey){

        return UserInquiry::where('name','like','%'.$searchKey.'%')
        ->orWhere('email','like','%'.$searchKey.'%')
        ->orWhere('phone','like','%'.$searchKey.'%')
        ->paginate(env("RECORDS_PER_PAGE"));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = ['image_src','title','description','status'];

    public static function getAdvertisementsForFilters($searchKey){

        return Advertisement::where('title','like','%'.$searchKey.'%')->paginate(env("RECORDS_PER_PAGE"));
    }
}

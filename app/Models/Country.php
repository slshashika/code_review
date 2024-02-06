<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table = "countries";
    protected $fillable = ['country_code','country_name','dial_code','status'];


    public static function getCountryForFilters($searchKey){

        return Country::where('country_name','like','%'.$searchKey.'%')->orderBy('id','desc')
        ->paginate(env("RECORDS_PER_PAGE"));
    }

}

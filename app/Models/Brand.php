<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $fillable = ['brand_name','brand_logo','brand_status','description'];

    public function brandImages(){
        return $this->hasMany(Image::class, 'entity_id','id')->where('type',3);
    }

    public static function getBrandsForFilters($searchKey){
        return Brand::with('brandImages')->where('brand_name','like','%'.$searchKey.'%')->paginate(env("RECORDS_PER_PAGE"));
    }

}

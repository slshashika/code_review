<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildCategory extends Model
{
    use HasFactory;

    const ACTIVE = 1;
    const INACTIVE = 0;

    const POST = 0;
    const PRODUCT = 1;

    protected $fillable=['child_category_name','child_category_description','slug','status','type','child_category_image','page_title','meta_tag_description','meta_keywords','canonical_url','sub_category_id'];

    public function products(){
        return $this->hasMany(Product::class)->with('images','promotion')->where('status',1);
    }

    
    public function subCategory(){
        return $this->belongsTo(SubCategory::class,'sub_category_id','id')->with('category');
    }

    public static function getCategoriesForFilters($searchKey){
        return ChildCategory::with('subCategory')->where('child_category_name','like','%'.$searchKey.'%')
        ->paginate(env("RECORDS_PER_PAGE"));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    const ACTIVE = 1;
    const INACTIVE = 0;

    const POST = 0;
    const PRODUCT = 1;

    protected $fillable = ['category_id','sub_category_name','sub_category_description','slug','status','sub_category_image','canonical_url','meta_keywords','meta_tag_description','page_title'];

    public function category(){
        return $this->belongsTo(Category::class,'category_id','id');
    }

    public function childCategories(){
        return $this->hasMany(ChildCategory::class);
    }

    public static function getSubCategoriesForFilters($searchKey){

        return SubCategory::with('category')->where('sub_category_name','like','%'.$searchKey.'%')
        ->paginate(env("RECORDS_PER_PAGE"));
    }
}

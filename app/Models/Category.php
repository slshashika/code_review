<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    const ACTIVE = 1;
    const INACTIVE = 0;

    const POST = 0;
    const PRODUCT = 1;

    protected $fillable=['category_name','category_description','slug','status','type','category_image','page_title','meta_tag_description','meta_keywords','canonical_url'];

    public function subCategories(){
        return $this->hasMany(SubCategory::class)->with('childCategories');
    }

    public function products(){
        return $this->hasMany(Product::class)->with('images','promotion','productVariants')->where('status',1);
    }
    public static function getCategoriesForFilters($searchKey){

        return Category::with('subCategories')->where('category_name','like','%'.$searchKey.'%')
        ->paginate(env("RECORDS_PER_PAGE"));
    }

    // *** Load active main categories for admin product drop-down
    public static function loadCategories(){
        return Category::where('status',1)->where('type',Category::PRODUCT)->get();
    }
}

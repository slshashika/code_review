<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = ['title','description','promotion_tag','discount_type','discount','type','ends_at','starts_at'];

    public function products(){
        return $this->hasMany(Product::class)->with('images','promotion','featuredImage','reviews')->where('status',1);
    }
    public static function getPromotionsForFilters($searchKey){

        return Promotion::where('title','like','%'.$searchKey.'%')->paginate(env("RECORDS_PER_PAGE"));
    }

    public static function deactivateExpiredPromotions(){

        $today = date('Y-m-d H:i:s');

        $promotions = Promotion::whereDate('ends_at','<=',$today)->get();

        foreach($promotions as $promotion){
            $promotion->status = 0;
            $promotion->save();
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = ['page_heading','slug','visibility','page_banner','entered_by','sort_order'];


    public function pageDescriptions(){
        return $this->hasOne(PageDescription::class)->where('language_id',0);
    }

    public function pageMetaData(){
        return $this->hasOne(PageMetaData::class);
    }

    public static function getAllPagesForFilters($searchKey){
        return Page::with('pageDescriptions','pageMetaData')
        ->where('page_heading','like','%'.$searchKey.'%')
        ->orderBy('sort_order','asc')
        ->paginate(env("RECORDS_PER_PAGE"));
    }

    public static function getPageForId($id){

        return Page::with('pageDescriptions','pageMetaData')
        ->where('id',$id)->get()->first();
    }

    public static function getAllVisiblePages(){
        return Page::with('pageDescriptions','pageMetaData')
        ->where('visibility',1)
        ->orderBy('sort_order','asc')
        ->get();
    }
}

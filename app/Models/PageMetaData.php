<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageMetaData extends Model
{
    use HasFactory;

    protected $table = 'page_meta_data';

    protected $fillable = ['page_id','page_title','meta_tag_description','meta_keywords','canonical_url'];

    public function page(){
        return $this->belongsTo(Page::class, 'page_id','id');
    } 

    public static function getPageMetaDataForPageId($page_id){
        return PageMetaData::where('page_id',$page_id)->get()->first();
    }
}

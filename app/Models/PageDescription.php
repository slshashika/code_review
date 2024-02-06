<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageDescription extends Model
{
    use HasFactory;

    protected $fillable = ['page_id','content','language_id'];

    public function page(){
        return $this->belongsTo(Page::class, 'page_id','id');
    } 

    public static function getPageDescriptionForPageId($page_id){
        return PageDescription::where('page_id',$page_id)->get()->first();
    }
}

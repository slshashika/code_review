<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    const POST = 0;
    const PRODUCT = 1;

    protected $fillable = ['tag_name','type'];

    
    public function posts(){
        return $this->belongsToMany(Post::class, 'post_tag');
    }

    public static function getTagsForFilters($type, $searchKey){

        return Tag::where('tag_name','like','%'.$searchKey.'%')
        ->where('type','=',$type)
        ->paginate(env("RECORDS_PER_PAGE"));
    }
}

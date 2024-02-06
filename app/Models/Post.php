<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Post extends Model
{
    use HasFactory;

    // types
    const BLOG = 0;
    const NEWS = 1;

    // status
    const UNPUBLISHED = 0;
    const PUBLISHED = 1;

    // approved status
    const NOT_APPROVED = 0;
    const APPROVED = 1;

    protected $fillable = ['title','body','status','type','user_id','is_approved','category_id','featured','slug'];

    public function postComments(){
        return $this->hasMany(Comment::class,'id','entity_id')->where('type',0);
    }

    public function category(){
        return $this->belongsTo(Category::class,'category_id','id');
    }
    
    public function tags(){
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function image(){
        return $this->hasOne(Image::class,'entity_id','id')->where('entity','post');
    }

    public static function getUserPostForId($id){
        if(Auth::user()->role_id == 2){
            return Post::with('tags','image')->where('id',$id)
            ->where('user_id',Auth::user()->id)->get()->first();
        } else {
            return Post::with('tags','image')->where('id',$id)
           ->get()->first();
        }
       
    }

    public static function getPostForId($id){
        return Post::where('id',$id)->get()->first();
    }

    public static function getPostsForFilters($searchKey){

        if(Auth::user()->role_id == 2){

            return Post::with('category')->where('title','like','%'.$searchKey.'%')
            ->where('user_id',Auth::user()->id)
            ->orderBy('id','desc')
            ->paginate(env("RECORDS_PER_PAGE"));

        }else{

            return Post::with('category')
            ->where('title','like','%'.$searchKey.'%')
            ->orderBy('id','desc')
            ->paginate(env("RECORDS_PER_PAGE"));
        }

        
    }

    public static function getPost($id){
        return Post::where('id',$id)->get()->first();
    }

    public static function getAllPostsToApprove($searchKey){

        return Post::where('title','like','%'.$searchKey.'%')
        ->where('is_approved',Post::NOT_APPROVED)
        ->orderBy('id','desc')
        ->paginate(env("RECORDS_PER_PAGE"));
    }
}

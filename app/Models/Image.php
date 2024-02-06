<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    //types
    const SLIDER = 0;
    const POST = 1;

    //status
    const NO_SHOW = 0;
    const SHOW = 1;

    protected $fillable = ['type','src','entity','entity_id','alt_text','heading','caption','status'];

    public static function getSliderImages(){
        return Image::where('type',Image::SLIDER)->where('status',Image::SHOW)->get();
    }
}

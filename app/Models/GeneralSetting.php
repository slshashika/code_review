<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = ['setting_key','description'];

    public static function getSettingByKey($key){
        
        return GeneralSetting::where('setting_key',$key)->get()->first();
    }
    
}

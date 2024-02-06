<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = ['section','template_number'];

    public function templates(){
        return $this->hasMany(SiteTemplate::class,'section','section');
    }

    public static function updateSiteSetting($section, $template){

        SiteSetting::where('section',$section)->update(['template_number' => (int)$template]);
    }
}

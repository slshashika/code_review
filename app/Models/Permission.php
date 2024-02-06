<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name','type'];

    public function users(){
        return $this->belongsToMany(Role::class, 'user_has_permissions');
    }

    public static function getPermissionsForFilters($searchKey){

        return Permission::where('name','like','%'.$searchKey.'%')->paginate(env("RECORDS_PER_PAGE"));
    }
}

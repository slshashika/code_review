<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'action','description'];

    public static function saveUserLog($userId, $action, $description){

        $userLog = new UserLog;

        $userLog->user_id = $userId;
        $userLog->action = $action;
        $userLog->description = $description;

        UserLog::create($userLog->toArray());
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Auth;

class Comment extends Model
{
    use HasFactory;

    const POST = 0;
    const PRODUCT = 1;

    const NEW_COMMENT = 0;
    const READ = 1;

    const NO_SHOW = 0;
    const SHOW = 1;

    protected $fillable = ['comment', 'entity', 'entity_id', 'user_id', 'type', 'reply_allowed', 'status', 'is_approved', 'show'];

    public function replies()
    {
        return $this->hasMany(CommentReply::class)->with('user');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'entity_id', 'id');
    }

    public static function getCommentsForPost($post_id)
    {
        return Comment::with('replies', 'user')->where('entity_id', $post_id)
            ->where('type', Comment::POST)->orderBy('created_at', 'desc')
            ->where('is_approved', 1)->paginate(env("RECORDS_PER_PAGE"));
    }

    public static function getAllCommentsForFilters($searchKey)
    {

        if (Auth::user()->role_id == 2) {

            return Comment::with('post', 'user', 'replies')
                ->where('entity', 0)
                ->where('type', 0)
                ->where('user_id', Auth::user()->id)
                ->where('comment', 'like', '%' . $searchKey . '%')
                ->orderBy('status', 'asc')
                ->orderBy('id', 'desc')->paginate(env("RECORDS_PER_PAGE"));
        } else {

            return Comment::with('post', 'user', 'replies')
                ->where('entity', 0)
                ->where('type', 0)
                ->where('comment', 'like', '%' . $searchKey . '%')
                ->orderBy('status', 'asc')
                ->orderBy('id', 'desc')->paginate(env("RECORDS_PER_PAGE"));
        }
    }

    public static function getCommentForId($id)
    {
        return Comment::where('id', $id)->get()->first();
    }
}

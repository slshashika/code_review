<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth', [App\Http\Controllers\APIControllers\AuthController::class, 'login']);
Route::post('/register', [App\Http\Controllers\APIControllers\AuthController::class, 'register']);


Route::prefix('web')->group(function () {
    
    // auth 
    Route::get('/auth/check', [App\Http\Controllers\APIControllers\AuthController::class, 'authCheck'])->middleware('auth:sanctum')->name('api.auth.check');
    Route::post('/auth/forgot-password', [App\Http\Controllers\APIControllers\AuthController::class, 'forgotPassword'])->name('api.auth.passwordReset');
    Route::get('/auth/user/{token}', [App\Http\Controllers\APIControllers\AuthController::class, 'getUserForResetPasswordToken'])->name('api.auth.user');
    Route::post('/auth/change-password', [App\Http\Controllers\APIControllers\AuthController::class, 'resetPassword'])->name('api.auth.changePassword');


    // home page controller
    Route::get('/home-content/get', [App\Http\Controllers\APIControllers\HomeAPIController::class, 'getAllHomeContent'])->name('api.home.get');
    Route::post('/subscribe', [App\Http\Controllers\APIControllers\HomeAPIController::class, 'subscribe'])->name('api.subscribe');

    //posts APIs
    Route::get('/posts/get', [App\Http\Controllers\APIControllers\PostAPIController::class, 'allPublishedPosts'])->name('api.posts.get');
    Route::post('/posts/search', [App\Http\Controllers\APIControllers\PostAPIController::class, 'allPublishedPostsForTitle'])->name('api.posts.title');
    Route::get('/posts/get/{slug}', [App\Http\Controllers\APIControllers\PostAPIController::class, 'getPostForSlug'])->name('api.posts.slug');
    Route::get('/posts/user/get/{id}', [App\Http\Controllers\APIControllers\PostAPIController::class, 'getAllPublishedPostsForUser'])->name('api.posts.user');
    Route::get('/posts/category/get/{slug}', [App\Http\Controllers\APIControllers\PostAPIController::class, 'getAllPublishedPostsForCategory'])->name('api.posts.category');
    Route::post('/posts/create', [App\Http\Controllers\APIControllers\PostAPIController::class, 'createPost'])->name('api.posts.create');
    Route::put('/posts/update/{id}', [App\Http\Controllers\APIControllers\PostAPIController::class, 'updatePost'])->name('api.posts.update');
    Route::post('/posts/publish-unpublish', [App\Http\Controllers\APIControllers\PostAPIController::class, 'publishUnpublishPost'])->name('api.posts.status');
    Route::delete('/posts/delete/{id}', [App\Http\Controllers\APIControllers\PostAPIController::class, 'deletePost'])->name('api.posts.delete');

    //post comments APIs    
    Route::post('/comments/create', [App\Http\Controllers\APIControllers\CommentAPIController::class, 'addPostComment'])->middleware('auth:sanctum')->name('api.comments.create');
    Route::post('/comments/reply', [App\Http\Controllers\APIControllers\CommentAPIController::class, 'replyForComment'])->middleware('auth:sanctum')->name('api.comments.reply');
    Route::delete('/comments/delete/{id}', [App\Http\Controllers\APIControllers\CommentAPIController::class, 'deleteComment'])->middleware('auth:sanctum')->name('api.comments.delete');
    Route::put('/comments/update', [App\Http\Controllers\APIControllers\CommentAPIController::class, 'editComment'])->middleware('auth:sanctum')->name('api.comments.update');
    Route::put('/comments/reply/update', [App\Http\Controllers\APIControllers\CommentAPIController::class, 'editCommentReply'])->middleware('auth:sanctum')->name('api.comments.editReply');

    //pages APIs
    Route::get('/pages/visible', [App\Http\Controllers\APIControllers\PagesAPIController::class, 'getAllVisiblePages'])->name('api.pages.visible');
    Route::get('/pages', [App\Http\Controllers\APIControllers\PagesAPIController::class, 'getAllPages'])->name('api.pages.all');
    Route::get('/pages/slug/{slug}', [App\Http\Controllers\APIControllers\PagesAPIController::class, 'getPageForSlug'])->name('api.pages.slug');
    

});

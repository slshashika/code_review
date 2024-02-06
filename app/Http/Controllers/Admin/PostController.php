<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\Image;
use App\Http\Controllers\Controller;
use Auth;
use Intervention\Image\Facades\Image as CompressImage;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('view_posts');

        if ($hasPermission) {

            $searchKey = $request->searchKey;

            $posts = Post::getPostsForFilters($searchKey);

            return view('admin.posts.all_posts', compact('posts', 'searchKey'));
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function newPostUI()
    {

        $hasPermission = Auth::user()->hasPermission('add_posts');

        if ($hasPermission) {

            $post_categories = Category::where('type', Category::POST)->get();
            $post_tags = Tag::where('type', Tag::POST)->get();

            return view('admin.posts.new_post', compact('post_categories', 'post_tags'));
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function store(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('add_posts');

        if ($hasPermission) {


            $validated = $request->validate(
                [
                    'title' => ['required', 'max:255'],
                    'body' => ['required'],
                    'type' => ['required'],
                    'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|dimensions:min_width=50,min_height=50,max_width=2000,max_height=2000',
                    'status' => ['required'],
                     'slug' => ['required', 'unique:posts']
                ],
                [
                    'image.required' => 'Category image required.',
                    'image.mimes' => 'Image types should be jpg,png,jpeg.',
                    'image.dimensions' => 'Please upload the images with the mentioned image dimentions.',

                ]
            );

            try {

                $post = new Post();

                $post->title = $request->title;
                $post->body = $request->body;
                $post->type = $request->type;
                $post->status = $request->status;
                $post->category_id = 1;
                $post->user_id = Auth::user()->id;
                $post->is_approved = Post::NOT_APPROVED;
                $post->featured = 1;
                $post->slug = $request->slug;

                $post->save();

                $post->tags()->attach($request->tags);

                $postImage = $request->image;

                if ($postImage != null) {


                    $destinationPath ="images/uploads/posts/";

                    $imageName =  date("YmdHis") . '_' . $postImage->getClientOriginalName();

                    // Open and resize the image
                    $image = CompressImage::make($postImage->getRealPath());
                    $image->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    // Save the compressed image
                    $image->save($destinationPath . $imageName, 50);

                    $imageUrl = $destinationPath . $imageName;

                    $newImage = new Image();

                    $newImage->type = Image::POST;
                    $newImage->src = $imageUrl;
                    $newImage->entity = 'post';
                    $newImage->entity_id = $post->id;

                    $newImage->save();
                }

                return back()->with('success', 'Your post "' . $request->title . '" submitted for the approval.');
            } catch (\Exception $exception) {
                $error = $exception->getMessage();
                return view("admin.errors.error_500", compact("error"));
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function editPostUI($id)
    {

        $hasPermission = Auth::user()->hasPermission('edit_posts');

        if ($hasPermission) {

            $post_categories = Category::where('type', Category::POST)->get();
            $post_tags = Tag::where('type', Tag::POST)->get();
            $post = Post::getUserPostForId($id);

            return view('admin.posts.edit_post', compact('post_categories', 'post_tags', 'post'));
        } else {

            return redirect('admin/not_allowed');
        }
    }


    public function update(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('edit_posts');

        if ($hasPermission) {

            $this->validate(
                $request,
                [
                    'title' => ['required', 'max:255'],
                    'body' => ['required'],
                    'type' => ['required'],
                    'status' => ['required'],
                    'image' => 'image|mimes:jpg,png,jpeg,gif,svg|dimensions:min_width=50,min_height=50,max_width=2000,max_height=2000',
                    'slug' => ['required', "unique:posts,slug,{$request->slug},slug"],
                ],
                [
                    'image.required' => 'Category image required.',
                    'image.mimes' => 'Image types should be jpg,png,jpeg.',
                    'image.dimensions' => 'Please upload the images with the mentioned image dimentions.',

                ]
            );


            $post = Post::getUserPostForId($request->post_id);

            if ($post != null) {

                $post->title = $request->title;
                $post->body = $request->body;
                $post->type = $request->type;
                $post->status = $request->status;
                $post->user_id = Auth::user()->id;
                $post->is_approved = Post::NOT_APPROVED;
                $post->slug = $request->slug;

                $post->save();

                //updating post image
                $postImage = $request->image;

                if ($postImage != null) {

                    $destinationPath ="images/uploads/posts/";
                    $imageName =  date("YmdHis") . '_' . $postImage->getClientOriginalName();

                    // Open and resize the image
                    $image = CompressImage::make($postImage->getRealPath());
                    $image->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    // Save the compressed image
                    $image->save($destinationPath . $imageName, 50);

                    $imageUrl = $destinationPath . $imageName;

                    $updateImage = Image::where('entity', 'post')->where('entity_id', $post->id)->get()->first();

                    if ($updateImage == null) {
                        $updateImage = new Image();

                        $updateImage->type = Image::POST;
                        $updateImage->entity = 'post';
                        $updateImage->entity_id = $post->id;
                    }

                    $updateImage->src = $imageUrl;

                    $updateImage->save();
                }

                return back()->with('success', 'Your post "' . $request->title . '" updated and submitted for the approval.');
            } else {
                return back()->with('error', 'Could not find the post.');
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function changeStatus($id)
    {

        $hasPermission = Auth::user()->hasPermission('page_published_status_change');

        if ($hasPermission) {

            try {

                $post = Post::getPostForId($id);


                if ($post != null) {
                    $msg = '';

                    if ($post->status == POST::UNPUBLISHED) {
                        // 0 status
                        $post->status = Post::PUBLISHED;
                        $msg = "Post status changed to published status.";
                    } else {
                        // 1 status
                        $post->status = Post::UNPUBLISHED;
                        $msg = "Post status changed to unpublished status.";
                    }

                    $post->save();

                    return back()->with('success', $msg);
                } else {
                    return back()->with('success', 'Could not find the post.');
                }
            } catch (\Exception $exception) {

                return back()->with('error', 'Error occured - ' . $exception->getMessage());
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function approvePost($id)
    {

        $hasPermission = Auth::user()->hasPermission('approve_posts');

        if ($hasPermission) {

            try {

                $post = Post::getPostForId($id);


                if ($post != null) {

                    $post->is_approved = Post::APPROVED;

                    $post->save();

                    return back()->with('success', 'Post approved successfully !');
                } else {
                    return back()->with('error', 'Could not find the post.');
                }
            } catch (\Exception $exception) {

                return back()->with('error', 'Error occured - ' . $exception->getMessage());
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function deletePost($id)
    {

        $hasPermission = Auth::user()->hasPermission('delete_posts');

        if ($hasPermission) {

            try {
                // deleting commments and replies

                $comments = Comment::getCommentsForPost($id);

                foreach ($comments as $comment) {
                    CommentReply::where('comment_id', $comment->id)->delete();
                    Comment::where('id', $comment->id)->delete();
                }

                // deleting post
                Post::where('id', $id)->delete();

                return back()->with('success', 'Post deleted successfully !');
            } catch (\Exception $exception) {
                return back()->with('error', 'Error occured - ' . $exception->getMessage());
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function postsToApproveUI(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('approve_posts');

        if ($hasPermission) {

            $searchKey = $request->searchKey;

            $posts = Post::getAllPostsToApprove($searchKey);

            return view('admin.posts.posts_to_approve', compact('posts', 'searchKey'));
        } else {

            return redirect('admin/not_allowed');
        }
    }
}

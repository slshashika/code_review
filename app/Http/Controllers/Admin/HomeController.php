<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Order;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {


        if (!Auth::user()->hasPermission('view_admin_panel')) {

            return Redirect::to('/');
        } else {

            $totalUsers = User::count();
            $totalPosts = Post::count();
            $totalPostsToApprove = Post::where('is_approved', 0)->count();
            $totalCommentsToApprove = Comment::where('is_approved', 0)->count();
            $pendingOrders = Order::where('order_status', 1)->count();
            $confirmedOrders = Order::where('order_status', 2)->count();
            $processing = Order::where('order_status', 3)->count();
            $dispatched = Order::where('order_status', 4)->count();
            $fulfilled = Order::where('order_status', 5)->count();
            $cancellation_Requested = Order::where('order_status', 6)->count();
            $cancelled = Order::where('order_status', 7)->count();

            return view('admin.dashboard', compact('totalUsers', 'totalPosts', 'totalPostsToApprove', 'totalCommentsToApprove', 'pendingOrders', 'confirmedOrders', 'processing', 'dispatched', 'fulfilled', 'cancellation_Requested', 'cancelled'));
        }
    }
}

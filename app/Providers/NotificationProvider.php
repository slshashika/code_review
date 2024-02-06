<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Post;
use App\Models\Comment;
use App\Models\UserInquiry;
use App\Models\Order;
use App\Models\GeneralSetting;
use App\Models\ProductInventory;

use Auth;
use View;

class NotificationProvider extends ServiceProvider
{
    public $notifications;
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
          
            $notApprovedPosts = Post::where('is_approved',0)->count();

            $newComments = Comment::where('is_approved',0)
            ->where('entity',Comment::POST)->count();

            
            $pendingOrders = Order::where('order_status',1)
            ->where('is_approved',0)->count();

            $cancellationApprovals = Order::where('order_status',6)->count();

            $newInquiries = UserInquiry::where('status',0)->count();

            $lowStockMargin = GeneralSetting::getSettingByKey('low_stock_margin');

            $lowStocks = ProductInventory::where('master_quantity','<=',$lowStockMargin->description)->count();


            $notifications = array();

            if($notApprovedPosts > 0){

                array_push($notifications, array(
                    'title' => "Not approved posts",
                    'message' => "You have ".$notApprovedPosts." not approved posts",
                    'route' => 'posts.approval'
                ));
            }

            if($newComments > 0){

                array_push($notifications, array(
                    'title' => "Not approved comments",
                    'message' => "You have ".$newComments." not approved comments",
                    'route' => 'postComments.all'
                ));
            }
            if($pendingOrders > 0){

                array_push($notifications, array(
                    'title' => "New orders",
                    'message' => $pendingOrders." new orders",
                    'route' => 'orders.all'
                ));
            }
            

            if($cancellationApprovals > 0){

                array_push($notifications, array(
                    'title' => "Cancellation approvals",
                    'message' => "You have ".$cancellationApprovals." cancellation approvals",
                    'route' => 'orders.cancellationApproval'
                ));
            }

            if($newInquiries > 0){

                array_push($notifications, array(
                    'title' => "New Inquiries",
                    'message' => "You have ".$newInquiries." new customer inquiries",
                    'route' => 'inquiries.all'
                ));
            }

            if($lowStocks > 0){

                array_push($notifications, array(
                    'title' => "Low Stock Products",
                    'message' => "You have ".$lowStocks." low stock products",
                    'route' => 'inventory.all'
                ));
            }
            
            View::share('notifications', $notifications);

    }
}

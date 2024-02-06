<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Mail\OrderPlacedMail;
use App\Mail\UserInquiryMail;
use App\Mail\OrderStatusMail;
use App\Mail\RegistrationEmail;
use App\Mail\LowStockEmail;

use Mail;
use Log;
use URL;

class EmailSender extends Model
{
    use HasFactory;


    public static function sendOrderPlacedMail($orderId)
    {

        Log::channel('email_log')->info("[Order Placed Email LOG] ====> Received request to send order placed email for the order id == " . $orderId);

        $response = response()->json([]);

        try {

            $order = Order::with('customer', 'orderItems', 'billingAddress')->where('id', $orderId)->get()->first();

            if ($order != null) {

                $siteLogo = GeneralSetting::getSettingByKey('site_logo');

                //customer email
                $details = array(
                    'subject' => config('app.name') . " - Order Placed - " . $order->tracking_number,
                    'introduction' => "Hi " . $order->customer->first_name . ",",
                    'order' => $order,
                    'link' =>  URL::to('/orders/order_tracking/' . $order->tracking_number),
                    'admin_alert' => 0,
                    'logo' => URL::to($siteLogo->description)
                );

                Mail::to($order->billingAddress->email)->send(new OrderPlacedMail($details));

                //admin order alert

                $adminEmailSettings = GeneralSetting::getSettingByKey('admin_email');

                if ($adminEmailSettings != null) {

                    $adminEmailDetails = array(
                        'subject' => config('app.name') . " - New Order Received - " . $order->tracking_number,
                        'introduction' => "Hi " . $order->customer->first_name . ",",
                        'order' => $order,
                        'link' =>  URL::to('/orders/order_tracking/' . $order->tracking_number),
                        'admin_alert' => 1,
                        'logo' => URL::to($siteLogo->description)
                    );

                    Mail::to($adminEmailSettings->description)->send(new OrderPlacedMail($adminEmailDetails));
                }


                $response = response()->json([
                    'status' => 'success',
                    'message' => 'Order placed mail sent successfully to email - ' . $order->customer->email,
                    'payload' => null
                ]);
            } else {

                $response = response()->json([
                    'status' => 'failed',
                    'message' => 'could not find the order for id - ' . $orderId,
                    'payload' => null
                ]);
            }
        } catch (\Exception $exception) {

            Log::channel('email_log')->info("[Order Placed Email LOG] ====>  Error occured when sending order placed email == " . $exception->getMessage() . ' - line - ' . $exception->getLine());

            $response = response()->json([
                'status' => 'failed',
                'message' => 'error occured',
                'payload' => $exception->getMessage() . ' - line - ' . $exception->getLine()
            ]);
        }

        Log::channel('email_log')->info("[Order Placed Email LOG] ====>  Returning response == " . json_encode($response));

        return $response;
    }






    public static function sendUserInquryEmail($inquiryId)
    {


        Log::channel('email_log')->info("[Inquiry email] ====> Received request to send inquiry placed email for the inquiry id == " . $inquiryId);

        $response = response()->json([]);

        try {


            $inquiry = UserInquiry::where('id', $inquiryId)->get()->first();

            if ($inquiry != null) {

                $siteLogo = GeneralSetting::getSettingByKey('site_logo');

                $details = array(
                    'subject' => config('app.name') . " - Inquiry Sumitted",
                    'email' =>  $inquiry->email,
                    'name' =>  $inquiry->name,
                    'phone' =>  $inquiry->phone,
                    'message' => $inquiry->message,
                    'introduction' => 'Hi ' . $inquiry->name . ',',
                    'logo' => URL::to($siteLogo->description)

                );

                Mail::to($inquiry->email)->send(new UserInquiryMail($details));

                $response = response()->json([
                    'status' => 'success',
                    'message' => 'Inquiry confirmation mail sent successfully to email - ' . $inquiry->email

                ]);
            } else {

                $response = response()->json([
                    'status' => 'failed',
                    'message' => 'could not find the Inquiry for id - ' . $inquiryId

                ]);
            }
        } catch (\Exception $exception) {

            Log::channel('email_log')->info("[Inquiry email] ====>  Error occured when sending inquiry email == " . $exception->getMessage() . ' - line - ' . $exception->getLine());

            $response = response()->json([
                'status' => 'failed',
                'message' => 'error occured',
                'payload' => $exception->getMessage() . ' - line - ' . $exception->getLine()
            ]);
        }

        Log::channel('email_log')->info("[Inquiry email] ====>  Returning response == " . json_encode($response));

        return $response;
    }



    public static function sendOrderStatusEmail($orderId)
    {


        Log::channel('email_log')->info("[Order Status Email] ====> Received request to send order status email for the order id == " . $orderId);

        $response = response()->json([]);

        try {


            $order = Order::with('customer', 'orderStatus', 'billingAddress')->where('id', $orderId)->get()->first();

            if ($order != null) {

                $siteLogo = GeneralSetting::getSettingByKey('site_logo');

                $details = array(
                    'subject' => config('app.name') . " - Order Status Update - " . $order->tracking_number,
                    'email' =>  $order->customer->email,
                    'name' =>  $order->customer->first_name,
                    'phone' =>  $order->customer->phone,
                    'introduction' => 'Hi ' . $order->customer->first_name . ',',
                    'message' => 'Current status of your order is - ' . $order->orderStatus->status_name,
                    'link' =>  URL::to('/orders/order_tracking/' . $order->tracking_number),
                    'logo' => URL::to($siteLogo->description)

                );

                Mail::to($order->billingAddress->email)->send(new OrderStatusMail($details));

                $response = response()->json([
                    'status' => 'success',
                    'message' => 'Order status email successfully to email - ' . $order->billingAddress->email

                ]);
            } else {

                $response = response()->json([
                    'status' => 'failed',
                    'message' => 'could not find the Inquiry for id - ' . $orderId

                ]);
            }
        } catch (\Exception $exception) {

            Log::channel('email_log')->info("[Order Status Email] ====>  Error occured when sending order status email == " . $exception->getMessage() . ' - line - ' . $exception->getLine());

            $response = response()->json([
                'status' => 'failed',
                'message' => 'error occured',
                'payload' => $exception->getMessage() . ' - line - ' . $exception->getLine()
            ]);
        }

        Log::channel('email_log')->info("[Order Status Email] ====>  Returning response == " . json_encode($response));

        return $response;
    }


    public static function sendRegistrationEmail($userId, $isCustomer)
    {


        Log::channel('email_log')->info("[Registration Email] ====> Received request to send user registration email for the order id == " . $userId);

        $response = response()->json([]);

        try {


            $user = User::where('id', $userId)->get()->first();

            if ($user != null) {

                $siteLogo = GeneralSetting::getSettingByKey('site_logo');

                $details = array(
                    'subject' => config('app.name') . " - Account Created ",
                    'email' =>  $user->email,
                    'introduction' => 'Hi ' . $user->first_name . ',',
                    'link' =>  URL::to('/'),
                    'contact_link' => URL::to('/contact-us'),
                    'is_customer' => $isCustomer,
                    'logo' => URL::to($siteLogo->description),

                );

                Mail::to($user->email)->send(new RegistrationEmail($details));

                $response = response()->json([
                    'status' => 'success',
                    'message' => 'User registration email successfully to email - ' . $user->email

                ]);
            } else {

                $response = response()->json([
                    'status' => 'failed',
                    'message' => 'could not find the user for id - ' . $userId

                ]);
            }
        } catch (\Exception $exception) {

            Log::channel('email_log')->info("[Registration Email] ====>  Error occured when registration email == " . $exception->getMessage() . ' - line - ' . $exception->getLine());

            $response = response()->json([
                'status' => 'failed',
                'message' => 'error occured',
                'payload' => $exception->getMessage() . ' - line - ' . $exception->getLine()
            ]);
        }

        Log::channel('email_log')->info("[Registration Email] ====>  Returning response == " . json_encode($response));

        return $response;
    }

    public static function sendLowStockEmail()
    {


        Log::channel('email_log')->info("[Low stock email] ====> Sending low stock email");

        $response = response()->json([]);

        try {

            $lowStockMargin = GeneralSetting::getSettingByKey('low_stock_margin');

            $lowStockInventories = ProductInventory::with('product')->where('master_quantity', '<', $lowStockMargin->description)->get();

            if (sizeof($lowStockInventories) > 0) {

                $adminEmail = GeneralSetting::getSettingByKey('admin_email');
                $siteLogo = GeneralSetting::getSettingByKey('site_logo');

                $details = array(
                    'subject' => config('app.name') . " - Low Stock Alert",
                    'email' =>  $adminEmail->description,
                    'message' => "Please find the low stock products mentions below.",
                    'introduction' => 'Hi User, ',
                    'logo' => URL::to($siteLogo->description),
                    'lowStockInventories' => $lowStockInventories

                );

                if ($adminEmail != null) {

                    Mail::to($adminEmail->description)->send(new LowStockEmail($details));

                    $response = response()->json([
                        'status' => 'success',
                        'message' => 'Low stock mail sent successfully to email - ' . $adminEmail->description

                    ]);
                } else {

                    $response = response()->json([
                        'status' => 'failed',
                        'message' => 'Admin email not found'

                    ]);
                }
            } else {

                $response = response()->json([
                    'status' => 'failed',
                    'message' => 'No low stock products found'

                ]);
            }
        } catch (\Exception $exception) {

            Log::channel('email_log')->info("[Low stock email] ====>  Error occured when sending low stock email == " . $exception->getMessage() . ' - line - ' . $exception->getLine());

            $response = response()->json([
                'status' => 'failed',
                'message' => 'error occured',
                'payload' => $exception->getMessage() . ' - line - ' . $exception->getLine()
            ]);
        }

        Log::channel('email_log')->info("[Low stock email] ====>  Returning response == " . json_encode($response));

        return $response;
    }
}

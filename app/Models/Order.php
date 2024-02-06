<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Session;
use Auth;

class Order extends Model
{
    // order statuses
    const PENDING = 1;
    const CONFIRMED = 2;
    const IN_PROCESS = 3;
    const DISPATHCED = 4;
    const FULFILLED = 5;
    const CANCELLATION_REQUESTED = 6;
    const CANCELLED = 7;

    //inventory statuses
    const NOT_RESERVED = 0;
    const RESERVED = 1;
    const INVENTORY_RETURNED = 2;

    use HasFactory;
    protected $fillable = ['customer_id','tracking_number','order_status', 'inventory_status','order_total','sub_total','discount','shipping_cost','total_weight','payment_method','zone_id','is_approved','cancelled_reason','next_statuses','notes','coupon_discount','coupon_id'];

    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id','id');
    }

    public function coupon(){

        return $this->belongsTo(Coupon::class,'coupon_id','id');
    }

    public function orderStatusHistories(){

        return $this->hasMany(OrderStatusHistory::class);
    }

    public function orderStatus(){
        return $this->belongsTo(OrderStatus::class,'order_status','id');
    }

    public function billingAddress(){
        return $this->hasOne(Address::class)->where('type',Address::BILLING);
    }

    public function shippingAddress(){
        return $this->hasOne(Address::class)->where('type',Address::SHIPPING);
    }

    public static function getInventoryNotReservedOrders($searchKey){

        return Order::with('orderItems','customer','billingAddress','shippingAddress','orderStatusHistories')
        ->where('inventory_status' ,0)
        ->where('tracking_number','like','%'.$searchKey.'%')->orderBy('id','desc')->paginate(env("RECORDS_PER_PAGE"));
    }

    public static function getOrdersForFilters($searchKey){

        return Order::with('orderItems','customer','billingAddress','shippingAddress','orderStatusHistories')
        ->where('tracking_number','like','%'.$searchKey.'%')->orderBy('id','desc')->paginate(env("RECORDS_PER_PAGE"));
    }

    public static function getOrdersForFiltersAndStatus($searchKey, $orderStatus){

        return Order::with('orderItems','customer','billingAddress','shippingAddress','orderStatusHistories')
        ->where('order_status',$orderStatus)
        ->where('tracking_number','like','%'.$searchKey.'%')->orderBy('id','desc')->paginate(env("RECORDS_PER_PAGE"));
    }

    public static function getApprovedOrdersForFilters($searchKey){

        return Order::with('orderItems','customer','billingAddress','shippingAddress','orderStatusHistories')
        ->where('is_approved',1)
        ->where('tracking_number','like','%'.$searchKey.'%')->orderBy('id','desc')->paginate(env("RECORDS_PER_PAGE"));
    }

    public static function getPendingCancellationOrdersForFilters($searchKey){

        return Order::with('orderItems','customer','billingAddress','shippingAddress','orderStatusHistories')
        ->where('id','like','%'.$searchKey.'%')
        ->where('order_status',Order::CANCELLATION_REQUESTED)
        ->where('tracking_number','like','%'.$searchKey.'%')->paginate(env("RECORDS_PER_PAGE"));

    }

    public static function getAllCancelledOrdersForFilters($searchKey){

        return Order::with('orderItems','customer','billingAddress','shippingAddress','orderStatusHistories')
        ->where('order_status',Order::CANCELLED)
        ->where('id','like','%'.$searchKey.'%')
        ->where('tracking_number','like','%'.$searchKey.'%')->paginate(env("RECORDS_PER_PAGE"));

    }

    public static function getOrderForId($id){

        return Order::with('orderItems','customer','billingAddress','shippingAddress','orderStatusHistories')
        ->where('id',$id)
        ->get()->first();

    }

    public static function GetOrderDetailsForFilters($searchKey, $orderStatus, $from, $to){

        $orders = array();

        $orders = Order::with('orderStatus','orderItems','customer','billingAddress','shippingAddress','orderStatusHistories')
        ->where('tracking_number','like','%'.$searchKey.'%')
        ->whereIn('order_status',$orderStatus)
        ->whereBetween('created_at',[$from, $to])
        ->paginate(env("RECORDS_PER_PAGE"));


        return $orders;
    }

    public static function GetAllOrderDetailsForFilters($searchKey, $orderStatus, $from, $to){

        $orders = array();

        $orders = Order::with('orderStatus','orderItems','customer','billingAddress','shippingAddress','orderStatusHistories')
        ->where('tracking_number','like','%'.$searchKey.'%')
        ->whereIn('order_status',$orderStatus)
        ->whereBetween('created_at',[$from, $to])
        ->get();


        return $orders;
    }

    public static function calculateOrderTotal($orderId){

        $order = Order::with('orderItems','customer')->where('id',$orderId)->get()->first();

        if($order != null){

            $orderTotal = 0.0;
            $orderSubtotal = 0.0;
            $discount = 0.0;
            $shippingCost = 0.0;
            $totalWeight = 0.0;

            foreach($order->orderItems as $orderItem){

                //subtotal calculation
                $product = Product::where('id',$orderItem->product_id)->with('promotion')->get()->first();
                $productVariant = Variant::where('product_id',$orderItem->product_id)->where('id',$orderItem['variant_id'])->get()->first();

                    $product_price = $productVariant->selling_price;
                    $discounted_price = $productVariant->selling_price;
                    $cartItemDiscount = 0.00;

                    if($product->promotion != null){

                        if($product->promotion->discount_type == 0){
                            $discounted_price = $product_price - $product->promotion->discount;
                            $cartItemDiscount = $product->promotion->discount;
                        }else{
                            $discounted_price = $product_price - ($product_price * $product->promotion->discount / 100);
                            $cartItemDiscount = $product_price * $product->promotion->discount / 100;
                        }

                    }


                $orderSubtotal = $orderSubtotal + ($orderItem->quantity * $product_price);

                //discount calculation
                $itemDiscount = $orderItem->quantity * $cartItemDiscount;


                $discount = $discount + $itemDiscount;

                //total weight calculation
                $totalWeight = $totalWeight + ($orderItem->quantity * $productVariant->weight);

                $orderItem->discount = $itemDiscount;
                $orderItem->save();

            }



            $orderTotal = $orderSubtotal + $shippingCost - $discount;

            $order->order_total = $orderTotal;
            $order->sub_total = $orderSubtotal;
            $order->discount = $discount;
            $order->shipping_cost = $shippingCost;
            $order->total_weight = $totalWeight;

            $order->save();

            // calculating order shipping cost
            $response = Order::calculateShippingCost($order->id);
            // if($order->zone_id != null){

            //     $zone = Zone::where('id',$order->zone_id)->get()->first();

            //     if($zone != null){

            //         $response = Order::calculateShippingCost($order->id);

            //     }
            //     $response = Order::calculateShippingCost($order->id);

            // }
        }
    }

    public static function calculateShippingCost($orderId){

        $order = Order::where('id',$orderId)->get()->first();

        $shipping_cost = 0.0;

        if($order != null){


            $freeShippingSetting = GeneralSetting::getSettingByKey('free_shipping');

            if($freeShippingSetting->description == 0){
                // zone base shipping + weight base
                $zone = Zone::where('id',$order->zone_id)->get()->first();
                if($zone != null){
                    if($order->total_weight > $zone->weight_margin){

                        $weight_factor = intdiv($order->total_weight, $zone->weight_margin);

                        $shipping_cost = $zone->minimum_cost + ($zone->shipping_cost * $weight_factor) + (fmod($order->total_weight, $zone->weight_margin) * $zone->shipping_cost);

                    }else{
                        $shipping_cost = $zone->minimum_cost;
                    }
                }
            }else if($freeShippingSetting->description == 2){
                //only weight base shipping
                $shippingCharge = ShippingCharge::loadShippingChargeMethods();
                if($order->total_weight > $shippingCharge->weight_margin){
                    $weight_factor = intdiv($order->total_weight, $shippingCharge->weight_margin);
                    $shipping_cost = $shippingCharge->weight_margin_cost + ($shippingCharge->additional_weight_cost * $weight_factor) + (fmod($order->total_weight, $shippingCharge->weight_margin) * $shippingCharge->additional_weight_cost);
                }else{
                    $shipping_cost = $shippingCharge->weight_margin_cost;
                }

            }

            $order->shipping_cost = $shipping_cost;
            $order->order_total = $order->order_total + $shipping_cost;

            $order->save();
        }

        return $shipping_cost;

    }





    public static function placeOrder(Request $request){

            $orderData = $request->order;
            $lineItems = $request->lineItems;
            $customerData = $request->billingAddress;
            $billingAddressData = $request->billingAddress;
            $shippingAddressData = $request->shippingAddress;

            if($orderData != null){

                $user_id = null;

                    if(Auth::user() != null){
                        $user_id = Auth::user()->id;
                    }
                //saving order
                $order = new Order;

                $order->order_total = $orderData['cart_total'];
                $order->order_status = 1; //pending
                $order->sub_total = $orderData['sub_total'];
                $order->discount = $orderData['discount'];
                $order->shipping_cost = $orderData['shipping_cost'];
                $order->total_weight = $orderData['weight'];
                $order->payment_method = $orderData['payment_method'];
                $order->notes = $request->orderNotes;

                if(Session::get('couponName') != null){
                    $coupon = Coupon::where('coupon_name',Session::get('couponName'))->get()->first();

                    if($coupon != null){
                        $order->coupon_discount = $coupon->coupon_type == 0 ? $coupon->coupon_value.' off' : $coupon->coupon_value.' % off';
                        $order->coupon_id = $coupon->id;
                    }

                }

                $saved_order = Order::create($order->toArray());

                $tracking_number = "ORD".date('YmdHis').$saved_order->id;

                Order::where('id',$saved_order->id)->update(['tracking_number' => $tracking_number]);

                //saving order status history

                OrderStatusHistory::saveOrderStatusHistory($saved_order->id, 1,$user_id);

                //saving line items

                $orderTotalWeight = 0.0;

                foreach($lineItems as $lineItem){

                    $product = Product::with('promotion')->where('id',$lineItem['product_id'])->get()->first();
                    $productVariant = Variant::where('product_id',$lineItem['product_id'])->where('id',$lineItem['variant_id'])->get()->first();

                    $item = new OrderItem;

                    $item->order_id = $saved_order->id;
                    $item->product_id = $lineItem['product_id'];
                    $item->variant_id = $lineItem['variant_id'];
                    $item->product_name = $product->product_name.' - '.$productVariant->variant_name;
                    $item->quantity = $lineItem['quantity'];
                    $item->unit_price = $productVariant->selling_price;
                    $item->total_price = (int)$lineItem['quantity'] * (float)$productVariant->selling_price;
                    $item->weight = (float)$productVariant->weight * (int)$lineItem['quantity'];

                    $cartItemDiscount = 0.00;

                    if($product->promotion != null){

                        if($product->promotion->discount_type == 0){
                            $cartItemDiscount = $product->promotion->discount;
                        }else{
                            $cartItemDiscount = $product->selling_price * $product->promotion->discount / 100;
                        }

                    }

                    $itemDiscount = $lineItem['quantity'] * $cartItemDiscount;

                    $item->discount = $itemDiscount;

                    $saved = OrderItem::create($item->toArray());

                    $orderTotalWeight = $orderTotalWeight + $item->weight;
                }

                Order::where('id',$saved_order->id)->update(['total_weight' => $orderTotalWeight]);

                //reserving inventory
                Product::reserveInventoryForOrderItems($saved_order->id);


                if($billingAddressData != null){
                    //saving customer

                    $saved_customer = null;
                    $user_id = null;

                    if(Auth::user()){
                        $user_id = Auth::user()->id;
                        $saved_customer = Customer::where('user_id', $user_id)->get()->first();
                    }

                    if($saved_customer == null){
                        $customer = new Customer;


                        $customer->first_name = $customerData['firstName'];
                        $customer->last_name = $customerData['lastName'];
                        $customer->email = $customerData['email'];
                        $customer->phone = $customerData['phone'];
                        $customer->address = $customerData['addressLine1'].' '.$customerData['addressLine2'];
                        $customer->user_id = $user_id;

                        $saved_customer = Customer::create($customer->toArray());

                    }


                    // updating order with customer id

                    $order_update = Order::where('id',$saved_order->id)->update(['customer_id' => $saved_customer->id]);

                    //saving billing address

                    $billingAddress = new Address;

                    $billingAddress->order_id = $saved_order->id;
                    $billingAddress->user_id = $user_id;
                    $billingAddress->type = Address::BILLING;
                    $billingAddress->customer_id = $saved_customer->id;
                    $billingAddress->first_name = $billingAddressData['firstName'];
                    $billingAddress->last_name = $billingAddressData['lastName'];
                    $billingAddress->email = $billingAddressData['email'];
                    $billingAddress->phone = $billingAddressData['phone'];
                    $billingAddress->address_line1 = $billingAddressData['addressLine1'];
                    $billingAddress->address_line2 = $billingAddressData['addressLine2'];
                    $billingAddress->country = $billingAddressData['country'];
                    $billingAddress->city = $billingAddressData['city'];
                    $billingAddress->zip = $billingAddressData['zipCode'];
                    $billingAddress->company = $billingAddressData['company'];
                    $billingAddress->state = $billingAddressData['state'];

                    $savedBillingAddress = Address::create($billingAddress->toArray());

                    if($billingAddressData['id'] == null){
                        // create new customer billing address

                        CustomerAddress::where('customer_id',$saved_customer->id)->where('type',0)->update(["active_status" => 0]);

                        $customerBillingAddress = new CustomerAddress;

                        $customerBillingAddress->type = 0;
                        $customerBillingAddress->customer_id = $saved_customer->id;
                        $customerBillingAddress->first_name = $billingAddressData['firstName'];
                        $customerBillingAddress->last_name = $billingAddressData['lastName'];
                        $customerBillingAddress->email = $billingAddressData['email'];
                        $customerBillingAddress->phone = $billingAddressData['phone'];
                        $customerBillingAddress->address_line1 = $billingAddressData['addressLine1'];
                        $customerBillingAddress->address_line2 = $billingAddressData['addressLine2'];
                        $customerBillingAddress->country = $billingAddressData['country'];
                        $customerBillingAddress->city = $billingAddressData['city'];
                        $customerBillingAddress->zip = $billingAddressData['zipCode'];
                        $customerBillingAddress->company = $billingAddressData['company'];
                        $customerBillingAddress->active_status = 1;
                        $customerBillingAddress->state = $billingAddressData['state'];

                        $savedCustomerBillingAddress = CustomerAddress::create($customerBillingAddress->toArray());


                    }

                }

                if($shippingAddressData != null){

                    //saving shipping address

                    $shippingAddress = new Address;

                    $shippingAddress->order_id = $saved_order->id;
                    $shippingAddress->type = Address::SHIPPING;
                    $shippingAddress->user_id = $user_id;
                    $shippingAddress->customer_id = $saved_customer->id;
                    $shippingAddress->first_name = $shippingAddressData['firstName'];
                    $shippingAddress->last_name = $shippingAddressData['lastName'];
                    $shippingAddress->email = $shippingAddressData['email'];
                    $shippingAddress->phone = $shippingAddressData['phone'];
                    $shippingAddress->address_line1 = $shippingAddressData['addressLine1'];
                    $shippingAddress->address_line2 = $shippingAddressData['addressLine2'];
                    $shippingAddress->country = $shippingAddressData['country'];
                    $shippingAddress->city = $shippingAddressData['city'];
                    $shippingAddress->zip = $shippingAddressData['zipCode'];
                    $shippingAddress->company = $shippingAddressData['company'];
                    $shippingAddress->state = $shippingAddressData['state'];

                    $savedShippingAddress = Address::create($shippingAddress->toArray());

                    // updating order zone

                    $zone = Zone::where('zip_code',$shippingAddressData['zipCode'])->get()->first();

                    if($zone != null){
                        Order::where('id',$saved_order->id)->update(['zone_id' => $zone->id]);
                    }


                    if($shippingAddressData['id'] == null){
                        // create new customer billing address

                        CustomerAddress::where('customer_id',$saved_customer->id)->where('type',1)->update(["active_status" => 0]);

                        $customerShippingAddress = new CustomerAddress;

                        $customerShippingAddress->type = 1;
                        $customerShippingAddress->customer_id = $saved_customer->id;
                        $customerShippingAddress->first_name = $shippingAddressData['firstName'];
                        $customerShippingAddress->last_name = $shippingAddressData['lastName'];
                        $customerShippingAddress->email = $shippingAddressData['email'];
                        $customerShippingAddress->phone = $shippingAddressData['phone'];
                        $customerShippingAddress->address_line1 = $shippingAddressData['addressLine1'];
                        $customerShippingAddress->address_line2 = $shippingAddressData['addressLine2'];
                        $customerShippingAddress->country = $shippingAddressData['country'];
                        $customerShippingAddress->city = $shippingAddressData['city'];
                        $customerShippingAddress->zip = $shippingAddressData['zipCode'];
                        $customerShippingAddress->company = $shippingAddressData['company'];
                        $customerShippingAddress->active_status = 1;
                        $customerShippingAddress->state = $shippingAddressData['state'];

                        $savedCustomerShippingAddress = CustomerAddress::create($customerShippingAddress->toArray());


                    }

                }


                //send order placed email
                $response = EmailSender::sendOrderPlacedMail($saved_order->id);

                return array(
                    'status' => true,
                    'message' => 'Order placed successfully !',
                    'url' => route('web.user.order.success', ['id' => $tracking_number])
                );


            }else{
                return array(
                    'status' => false,
                    'message' => 'Invalid order data. Please try again.',
                    'url' => null
                );
            }
    }


    public static function calculateCartTotal($cart){

        if($cart != null){

            $orderTotal = 0.0;
            $orderSubtotal = 0.0;
            $discount = 0.0;
            $shippingCost = 0.0;
            $totalWeight = 0.0;
            $orderItems = array();

            foreach($cart as $orderItem){

                $product = Product::where('id',$orderItem['product_id'])->with('promotion')->get()->first();
                $productVariant = Variant::where('product_id',$orderItem['product_id'])->where('id',$orderItem['variant_id'])->get()->first();

                if($product != null and $productVariant != null){

                    $product_price = $productVariant->selling_price;
                    $discounted_price = $productVariant->selling_price;
                    $cartItemDiscount = 0.00;

                    if($product->promotion != null){

                        if($product->promotion->discount_type == 0){
                            $discounted_price = $product_price - $product->promotion->discount;
                            $cartItemDiscount = $product->promotion->discount;
                        }else{
                            $discounted_price = $product_price - ($product_price * $product->promotion->discount / 100);
                            $cartItemDiscount = $product_price * $product->promotion->discount / 100;
                        }

                    }

                    //subtotal calculation

                    $orderSubtotal = $orderSubtotal + ($orderItem['qty'] * $product_price);

                    //discount calculation
                    $itemDiscount = $orderItem['qty'] * $cartItemDiscount;

                    $discount = $discount + $itemDiscount;

                    //total weight calculation
                    $totalWeight = $totalWeight + ($orderItem['qty'] * $productVariant->weight);

                    // $orderItem->discount = $itemDiscount;
                    array_push($orderItems,array('product_id' => $orderItem['product_id'], 'variant_id' => $orderItem['variant_id'], 'quantity' => $orderItem['qty']));
                }

            }


            $freeShippingSetting = GeneralSetting::getSettingByKey('free_shipping');

            if($freeShippingSetting->description == 0){

                $shippingAddress = Session::get('shippingAddress');

                if($shippingAddress != null){
                    $zone = Zone::where('zip_code',$shippingAddress['zipCode'])->get()->first();

                    if($zone != null){

                        if($totalWeight > $zone->weight_margin){

                            $shippingCost = $zone->minimum_cost;

                            $remainingWeight = $totalWeight - $zone->weight_margin;

                            $extraShippingCost = $remainingWeight + $zone->shipping_cost;

                            $shippingCost = $shippingCost + $extraShippingCost;



                        }else{
                            $shippingCost = $zone->minimum_cost;
                        }

                    }

                    // $shippingCost = 200;
                }

            }
            else if($freeShippingSetting->description == 2){

                //only weight base shipping
                $shippingCharge = ShippingCharge::loadShippingChargeMethods();

                if($totalWeight > $shippingCharge->weight_margin){
                    $weight_factor = intdiv($totalWeight, $shippingCharge->weight_margin);
                    $shippingCost = $shippingCharge->weight_margin_cost + ($shippingCharge->additional_weight_cost * $weight_factor) + (fmod($totalWeight, $shippingCharge->weight_margin) * $shippingCharge->additional_weight_cost);
                }else{
                    $shippingCost = $shippingCharge->weight_margin_cost;
                }

            }

            $orderTotal = $orderSubtotal + $shippingCost - $discount;


            return array(
                'cart_total' => $orderTotal,
                'sub_total' => $orderSubtotal,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'weight' => $totalWeight,
                'cartItems' => $orderItems,
                'payment_method' => "COD"
            );


        }else{
            return array(
                'cart_total' => 0.00,
                'sub_total' => 0.00,
                'discount' => 0.00,
                'shipping_cost' => 0.00,
                'weight' => 0.00,
                'cartItems' => array(),
                'payment_method' => "COD"
            );
        }
    }


    public static function calculateCouponDiscounts($couponName){


        $couponsEnabled = GeneralSetting::getSettingByKey('coupons_enabled');

        $cart = session()->has('cart') ? session()->get('cart') : [];

        //calculate only when coupons are enabled
        if($couponsEnabled->description == "1"){

            if($couponName != null){

                $today = date('y-m-d H:i:s');


                $coupon = Coupon::where('coupon_name',$couponName)->where('status',1)
                ->where('expiry_date', '>=', $today)->get()->first();

                if($coupon != null){

                    Session::put('couponName',$couponName);

                    $response = Order::calculateCartTotal($cart);

                    $cartSubtotal = $response['sub_total'];
                    $cartTotal = $response['cart_total'];
                    $cartDiscount = $response['discount'];

                    if($coupon->coupon_type == 0){

                        $cartDiscount = $cartDiscount + $coupon->coupon_value;
                        // $cartSubtotal = $cartSubtotal - $coupon->coupon_value;
                        $cartTotal = $cartTotal - $coupon->coupon_value;

                    }else{

                        $couponDiscountValue = $cartSubtotal * $coupon->coupon_value / 100;

                        $cartDiscount = $cartDiscount + $couponDiscountValue;
                        // $cartSubtotal = $cartSubtotal - $couponDiscountValue;
                        $cartTotal = $cartTotal - $couponDiscountValue;

                    }

                    $response['sub_total'] = $cartSubtotal;
                    $response['cart_total'] = $cartTotal;
                    $response['discount'] = $cartDiscount;



                    Session::put('cartValues',$response);
                    Session::forget('couponMessage');


                }else{

                    $response = Order::calculateCartTotal($cart);
                    Session::put('cartValues',$response);

                    Session::put('couponMessage',"The coupon name you entered is not a valid coupon");
                    Session::forget('couponName');

                }

            }else{
                $response = Order::calculateCartTotal($cart);
                Session::put('cartValues',$response);
                Session::forget('couponName');
                Session::forget('couponSuccessMesage');
            }

        }else {

            $response = Order::calculateCartTotal($cart);
            Session::put('cartValues',$response);
            Session::put('couponMessage',"Sorry. Coupons are not enabled");
        }



    return true;

}

}

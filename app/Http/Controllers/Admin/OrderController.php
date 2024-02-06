<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Customer;
use App\Models\Address;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Zone;
use App\Models\User;
use App\Models\Hamper;
use App\Models\OrderStatus;
use App\Models\EmailSender;
use App\Models\OrderStatusHistory;
use App\Models\UserLog;
use App\Models\GeneralSetting;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Session;
use Auth;
use PDF;
use ConsoleTVs\Charts\Facades\Charts as Charts;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function index(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('view_orders');

        if ($hasPermission) {

            $searchKey = $request->searchKey;
            $selectedOrderStatus = $request->selectedOrderStatus;

            $orders = array();

            if ($selectedOrderStatus == null or $selectedOrderStatus == "all") {
                $orders = Order::getOrdersForFilters($searchKey);
            } else {
                $orders = Order::getOrdersForFiltersAndStatus($searchKey, $selectedOrderStatus);
            }


            $orders->appends(request()->query())->links();
            $zones = Zone::all();
            $orderStatuses = OrderStatus::all();

            foreach ($orders as $order) {
                $order->next_statuses = OrderStatus::where('id', '>', $order->order_status)->get();
            }

            return view('admin.orders.all_orders', compact('orders', 'searchKey', 'zones', 'selectedOrderStatus', 'orderStatuses'));

        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function changeOrderStatusUI(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('change_order_status');

        if ($hasPermission) {

            $searchKey = $request->searchKey;
            $orders = Order::getApprovedOrdersForFilters($searchKey);

            foreach ($orders as $order) {
                $order->next_statuses = OrderStatus::where('id', '>', $order->order_status)->get();
            }
            return view('admin.orders.change_order_status', compact('orders', 'searchKey'));
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function cancellationApprovals(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('approve_cancellations');

        if ($hasPermission) {

            $searchKey = $request->searchKey;
            $orders = Order::getPendingCancellationOrdersForFilters($searchKey);
            return view('admin.orders.approve_cancellations', compact('orders', 'searchKey'));
        } else {

            return redirect('admin/not_allowed');
        }
    }


    public function store(Request $request)
    {

        try {

            $response = Order::placeOrder($request);

            if ($response['status']) {
                return back()->with('success', $response['message']);
            } else {
                $error = $response['message'];
                return view('admin.errors.error_500', compact('error'));
            }
        } catch (\Exception $exception) {

            $error = $exception->getMessage() . ' - line - ' . $exception->getLine();
            return view('admin.errors.error_500', compact('error'));
        }
    }

    public function inventoryNotReservedOrders(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('manual_inventory_reserve');

        if ($hasPermission) {

            $searchKey = $request->searchKey;
            $orders = $orders = Order::getInventoryNotReservedOrders($searchKey);

            return view('admin.orders.not_reserved_orders', compact('searchKey', 'orders'));
        } else {

            return redirect('admin/not_allowed');
        }
    }


    public function reserveInventoryManually($id)
    {

        try {

            //reserving inventory
            $response = Product::reserveInventoryForOrderItems($id);

            if ($response) {

                $order = Order::where('id', $id)->get()->first();
                if (Auth::user()) {
                    //saving user log
                    UserLog::saveUserLog(Auth::user()->id, "Inventory reserved manually", "Inventory reserved manually for order " . $order->tracking_number);
                }
                return back()->with('success', 'Inventory reservation succeeded !');
            } else {

                return back()->with('error', 'Inventory reservation failed due to insufficient inventory');
            }
        } catch (\Exception $exception) {

            $error = $exception->getMessage() . ' - line - ' . $exception->getLine();
            return view('admin.errors.error_500', compact('error'));
        }
    }


    public function changeOrderStatus(Request $request)
    {


        $hasPermission = Auth::user()->hasPermission('change_order_status');

        if ($hasPermission) {

            $order = Order::where('id', $request->order_id)->get()->first();

            if ($order != null) {

                $order->order_status = $request->order_status;
                $order->save();

                //saving order status history
                OrderStatusHistory::saveOrderStatusHistory($order->id, $request->order_status, Auth::user()->id);

                EmailSender::sendOrderStatusEmail($order->id);

                $orderStatus = OrderStatus::where('id', $request->order_status)->get()->first();

                if (Auth::user()) {
                    //saving user log
                    UserLog::saveUserLog(Auth::user()->id, "Order status changed", "Order status changed in order " . $order->tracking_number . " to " . $orderStatus->status_name . " status");
                }

                return back()->with('success', 'Order status changes successfully !');
            } else {

                return back()->with('error', 'Could not find the order.');
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }


    public function approveOrderCancellation($id)
    {

         //if the cancelling order is an inventory reserved order the inventory should be returned on cancellation approval
        try {

            $hasPermission = Auth::user()->hasPermission('approve_cancellations');

            if ($hasPermission) {

                $order = Order::where('id', $id)->get()->first();

                if ($order != null) {

                    $order->order_status = 7; //cancelled status

                    $order->save();

                    //saving order status history
                    OrderStatusHistory::saveOrderStatusHistory($order->id, 7, Auth::user()->id);

                    //returning inventory of the cancelled order
                    Product::returnInventoryOfCancelledOrder($order->id);

                    EmailSender::sendOrderStatusEmail($order->id);

                    if (Auth::user()) {
                        //saving user log
                        UserLog::saveUserLog(Auth::user()->id, "Order cancellation approved", "Order cancellation approved in order " . $order->tracking_number);
                    }

                    return back()->with('success', 'Order cancelled and inventory returned successfully !');
                } else {

                    return back()->with('error', 'Could not find the order');
                }
            } else {

                return redirect('admin/not_allowed');
            }
        } catch (\Exception $exception) {

            $error = $exception->getMessage() . ' - line - ' . $exception->getLine();
            return view('admin.errors.error_500', compact('error'));
        }
    }


    public function getOrderPage(Request $request)
    {
         return view('admin.order.all_order');
    }


    public function showOrder()
    {

        $categories = Order::all();
         return view('admin.order.all_order')->with(['categories' => $categories]);
    }


    public function editOrder($id)
    {

        $hasPermission = Auth::user()->hasPermission('edit_orders');

        if ($hasPermission) {

            $order = Order::with('orderItems', 'customer', 'billingAddress', 'shippingAddress')
                ->where('id', $id)->get()->first();

            return view('admin.orders.edit_order', compact('order'));
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function editOrderItemQuantity(Request $request)
    {


        try {

            $hasPermission = Auth::user()->hasPermission('edit_orders');

            if ($hasPermission) {

                $itemId = $request->order_item_id;
                $quantity = $request->quantity;

                $item = OrderItem::where('id', $itemId)->get()->first();

                if ($item != null) {

                    //check for inventory

                    $item->quantity = $quantity;
                    $item->save();

                    // need to re-calculate order amounts after item quantity change
                    Order::calculateOrderTotal($item->order_id);

                    $orderItem = Order::where('id', $item->order_id)->get()->first();

                    if (Auth::user()) {
                        //saving user log
                        UserLog::saveUserLog(Auth::user()->id, "Order item quantity edited", "Order item quantity edited in order " . $orderItem->tracking_number . " in item " . $item->hamper_name);
                    }

                    return back()->with('success', 'Item quantity updated successfully !');
                } else {
                    return back()->with('error', 'Could not find the item');
                }


                $order = Order::with('orderItems', 'customer', 'billingAddress', 'shippingAddress')->where('id', $itemId)->get()->first();

                return view('admin.orders.edit_order', compact('order'));
            } else {

                return redirect('admin/not_allowed');
            }
        } catch (\Exception $exception) {

            $error = $exception->getMessage() . ' - line - ' . $exception->getLine();
            return view('admin.errors.error_500', compact('error'));
        }
    }

    public function removeOrderItem($id)
    {

        try {

            $hasPermission = Auth::user()->hasPermission('edit_orders');

            if ($hasPermission) {


                $item = OrderItem::where('id', $id)->get()->first();
                $deleted = OrderItem::where('id', $id)->delete();

                if ($deleted) {

                    // need to re-calculate order amounts after item deletion
                    Order::calculateOrderTotal($item->order_id);

                    $order = Order::where('id', $item->order_id)->get()->first();

                    if (Auth::user()) {
                        //saving user log
                        UserLog::saveUserLog(Auth::user()->id, "Order item deleted", "Order item deleted in order " . $order->tracking_number . " in item " . $item->hamper_name);
                    }

                    return back()->with('success', 'Order item removed successfully !');
                } else {
                    return back()->with('error', 'Failed to remove order item !');
                }
            } else {

                return redirect('admin/not_allowed');
            }
        } catch (\Exception $exception) {

            $error = $exception->getMessage() . ' - line - ' . $exception->getLine();
            return view('admin.errors.error_500', compact('error'));
        }
    }

    public function approveOrder(Request $request)
    {

        try {

            $hasPermission = Auth::user()->hasPermission('change_order_status');

            if ($hasPermission) {

                $order = Order::where('id', $request->order_id)->get()->first();

                if ($order != null) {

                    $order->is_approved = 1;
                    $order->order_status = 2; //order approved status

                    $order->save();

                    // need to re-calculate order amounts after item deletion
                    Order::calculateOrderTotal($order->id);

                    EmailSender::sendOrderStatusEmail($order->id);


                    if (Auth::user()) {
                        //saving user log
                        UserLog::saveUserLog(Auth::user()->id, "Order approved", "Order approved " . $order->tracking_number);
                    }

                    return back()->with('success', 'Zone assigned and order approved successfully !');
                } else {

                    return back()->with('error', 'Could not find the order !');
                }
            } else {

                return redirect('admin/not_allowed');
            }
        } catch (\Exception $exception) {

            $error = $exception->getMessage() . ' - line - ' . $exception->getLine();
            return view('admin.errors.error_500', compact('error'));
        }
    }

    public function initiateOrderCancellation(Request $request)
    {

        try {

            $hasPermission = Auth::user()->hasPermission('cancel_orders');

            if ($hasPermission) {

                $order = Order::where('id', $request->order_id)->get()->first();

                if ($order != null) {

                    $order->order_status = 6; //cancellation requested status
                    $order->cancelled_reason = $request->cancelled_reason;

                    $order->save();

                    //saving order status history
                    OrderStatusHistory::saveOrderStatusHistory($order->id, 6, Auth::user()->id);

                    EmailSender::sendOrderStatusEmail($order->id);

                    if (Auth::user()) {
                        //saving user log
                        UserLog::saveUserLog(Auth::user()->id, "Order cancellation requested", "Order cancellation requested for order " . $order->tracking_number);
                    }

                    return back()->with('success', 'Order cancellation requested and order updated successfully !');
                } else {

                    return back()->with('error', 'Could not find the order');
                }
            } else {

                return redirect('admin/not_allowed');
            }
        } catch (\Exception $exception) {

            $error = $exception->getMessage() . ' - line - ' . $exception->getLine();
            return view('admin.errors.error_500', compact('error'));
        }
    }

    public function cancelledOrders(Request $request)
    {

        $searchKey = $request->searchKey;
        $orders = Order::getAllCancelledOrdersForFilters($searchKey);
        return view('admin.orders.cancelled_orders', compact('orders', 'searchKey'));
    }

    public function updateBillingShippingAddresses(Request $request)
    {

        try {

            $hasPermission = Auth::user()->hasPermission('edit_orders');

            if ($hasPermission) {

                $address = Address::where('id', $request->address_id)->get()->first();


                if ($address != null) {

                    $address->first_name = $request->first_name;
                    $address->last_name = $request->last_name;
                    $address->email = $request->email;
                    $address->phone = $request->phone;
                    $address->address_line1 = $request->address_line1;
                    $address->address_line2 = $request->address_line2;
                    $address->country = $request->country;
                    $address->city = $request->city;
                    $address->zip = $request->zip;
                    $address->company = $request->company;

                    $address->save();

                    $addressType = "Billing";
                    if ($address->type == 1) {
                        $addressType = "Shipping";
                    }

                    if (Auth::user()) {
                        //saving user log
                        UserLog::saveUserLog(Auth::user()->id, "Order address updated", "Order " . $addressType . " address updated");
                    }
                }

                return back()->with('success', 'Address details updated successfully !');
            } else {

                return redirect('admin/not_allowed');
            }
        } catch (\Exception $exception) {

            $error = $exception->getMessage() . ' - line - ' . $exception->getLine();
            return view('admin.errors.error_500', compact('error'));
        }
    }

    public function updateOrderCustomer(Request $request)
    {

        try {

            $hasPermission = Auth::user()->hasPermission('edit_orders');

            if ($hasPermission) {


                $customer = Customer::where('id', $request->customer_id)->get()->first();

                if ($customer != null) {

                    $customer->first_name = $request->customer_first_name;
                    $customer->last_name = $request->customer_last_name;
                    $customer->email = $request->customer_email;
                    $customer->phone = $request->customer_phone;
                    $customer->address = $request->customer_address;

                    $customer->save();

                    if ($customer->user_id != null) {

                        $user = User::where('id', $customer->user_id)->get()->first();

                        if ($user != null) {

                            $user->first_name = $request->customer_first_name;
                            $user->last_name = $request->customer_last_name;
                            $user->phone = $request->customer_phone;

                            $user->save();
                        }
                    }

                    if (Auth::user()) {
                        //saving user log
                        UserLog::saveUserLog(Auth::user()->id, "Order customer updated", "Order customer updated in order for email " . $customer->email);
                    }

                    return back()->with('success', 'Customer updated successfully !');
                } else {

                    return back()->with('error', 'Could not find the customer !');
                }
            } else {

                return redirect('admin/not_allowed');
            }
        } catch (\Exception $exception) {

            $error = $exception->getMessage() . ' - line - ' . $exception->getLine();
            return view('admin.errors.error_500', compact('error'));
        }
    }

    public function showOrderStatuses(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('view_order_status');

        if ($hasPermission) {

            $searchKey = $request->searchKey;
            $orderStatuses = OrderStatus::where('status_name', 'like', '%' . $searchKey . '%')->paginate(env("RECORDS_PER_PAGE"));



            return view('admin.orders.all_order_statuses', compact('searchKey', 'orderStatuses'));
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function createOrderStatus(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('add_order_status');

        if ($hasPermission) {


            $orderStatus = new OrderStatus;

            $orderStatus->status_name = $request->status_name;

            OrderStatus::create($orderStatus->toArray());

            if (Auth::user()) {
                //saving user log
                UserLog::saveUserLog(Auth::user()->id, "New order status created", "New order status created. status " . $request->status_name);
            }


            return back()->with('success', 'Order status created successfully !');
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function updateOrderStatus(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('edit_order_status');

        if ($hasPermission) {


            $orderStatus = OrderStatus::where('id', $request->status_id)->get()->first();

            if ($orderStatus != null) {

                $orderStatus->status_name = $request->status_name;
                $orderStatus->save();

                if (Auth::user()) {
                    //saving user log
                    UserLog::saveUserLog(Auth::user()->id, "Order status updated", "Order status updated. status " . $orderStatus->status_name);
                }


                return back()->with('success', 'Order status updated successfully !');
            } else {

                return back()->with('error', 'Could not find the order status !');
            }
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function removeOrderStatus($id)
    {

        $hasPermission = Auth::user()->hasPermission('remove_order_status');

        if ($hasPermission) {


            $orderStatus = OrderStatus::where('id', $id)->get()->first();

            if (Auth::user()) {
                //saving user log
                UserLog::saveUserLog(Auth::user()->id, "Order status removed", "Order status updated. status " . $orderStatus->status_name);
            }

            OrderStatus::where('id', $id)->delete();


            return back()->with('success', 'Order status removed successfully !');
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function downloadOrderInvoice($id)
    {

        $order = Order::getOrderForId($id);
        $customer = $order->customer;

        if ($order != null) {
            $siteLogo = GeneralSetting::getSettingByKey('site_logo');

            $logo = "data:image/png;base64," . base64_encode(file_get_contents($siteLogo->description));

            $pdf = PDF::loadView('admin/orders/templates/order_invoice', compact('order', 'customer', 'logo'));
            $pdf->setPaper('A4', 'potrait');
            // return view('admin.orders.templates.order_invoice',compact('order','customer','logo'));

            // return $pdf->stream("dompdf_out.pdf", array("Attachment" => false));
            // dd($pdf);
            return $pdf->download($order->tracking_number . '_invoice.pdf');
        } else {
            return back()->with('error', 'Could not find the order');
        }
    }

    public function downloadPackingSlip($id)
    {

        $order = Order::getOrderForId($id);
        $customer = $order->customer;


        if ($order != null) {

            $siteLogo = GeneralSetting::getSettingByKey('site_logo');

            $logo = "data:image/png;base64," . base64_encode(file_get_contents($siteLogo->description));

            $pdf = PDF::loadView('admin/orders/templates/packing_slip', compact('order', 'customer', 'logo'));
            $pdf->setPaper('A5', 'landscape');
            // return view('admin.orders.templates.packing_slip',compact('order','customer','logo'));

            // return $pdf->stream("dompdf_out.pdf", array("Attachment" => false));
            // dd($pdf);
            return $pdf->download($order->tracking_number . '_packing.pdf');
        } else {
            return back()->with('error', 'Could not find the order');
        }
    }

    public function invoicesAndPackingSlips(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('invoices_and_packing_slips');

        if ($hasPermission) {

            $searchKey = $request->searchKey;
            $orders = Order::getApprovedOrdersForFilters($searchKey);

            return view('admin.orders.invoices_and_packing_slips', compact('orders', 'searchKey'));
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function viewQuotations(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission('view_quotations');

        if ($hasPermission) {

            $searchKey = $request->searchKey;

            $quotations = Quotation::getQuotationsForFilters($searchKey);

            return view('admin.orders.all_quotations', compact('quotations', 'searchKey'));
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function downloadQuotation($id)
    {

        $hasPermission = Auth::user()->hasPermission('view_quotations');

        if ($hasPermission) {


            $quotation = Quotation::with('quotationItems')->where('id', $id)->get()->first();

            $siteLogo = GeneralSetting::getSettingByKey('site_logo');

            $logo = "data:image/png;base64," . base64_encode(file_get_contents($siteLogo->description));

            // return view('frontend/cart/templates/cart_quotation',compact('cart','total_price','logo','total_weight'));

            $pdf = PDF::loadView('admin/orders/templates/cart_quotation', compact('quotation', 'logo'));

            $pdf->setPaper('A4', 'potrait');

            return $pdf->download($quotation->reference_number . '.pdf');
        } else {

            return redirect('admin/not_allowed');
        }
    }

    public function salesOrderCharts(Request $request)
    {
        // dd($request->all());
        $labels = [2, 5, 7, 4, 8];
        $data  = [10, 20, 30, 40, 50];

        $f_date = $request->from_date;
        $t_date = $request->to_date;
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date);

        if ($request->from_date != null && $request->to_date != null) {

            $orders = Order::with('orderItems', 'customer', 'billingAddress', 'shippingAddress', 'orderStatusHistories')
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->orderBy('id', 'desc')->paginate(env("RECORDS_PER_PAGE"));
        } else {

            $orders = Order::with('orderItems', 'customer', 'billingAddress', 'shippingAddress', 'orderStatusHistories')
                ->orderBy('id', 'desc')->paginate(env("RECORDS_PER_PAGE"));
        }

        if ($request->download == 1) {

            return $this->downloadOrderReport($orders);
        }

        return view('admin.orders.salesReports', compact('labels', 'data', 'orders', 'f_date', 't_date'));
    }

    public function downloadOrderReport($orders)
    {

        $fileName = date('Y_m_d') . '_sales_report.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('ORDER TRACKING NUMBER', 'STATUS', 'INVENTORY STATUS', 'POSTAL CODE', 'STATE', 'SUBURB', 'TOTAL (RS.)');

        try {
            $callback = function () use ($orders, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($orders as $order) {

                    $status = 'Pending';
                    $inventory_status = '';

                    if ($order->inventory_status == 0) {
                        $inventory_status = 'Not Reserved';
                    } else if ($order->inventory_status == 1) {
                        $inventory_status = 'Reserved';
                    } else {
                        $inventory_status = 'Returned';
                    }


                    if ($order->order_status == 1) {
                        $status = 'Pending';
                    } else if ($order->order_status == 2) {
                        $status = 'Confirmed';
                    } else if ($order->order_status == 3) {
                        $status = 'Dispatched';
                    } else if ($order->order_status == 4) {
                        $status = 'Fulfilled';
                    } else {
                        $status = 'Cancelled';
                    }

                    $row['ORDER TRACKING NUMBER']  = $order->tracking_number;
                    $row['STATUS']    = $status;
                    $row['INVENTORY STATUS']    = $inventory_status;
                    $row['POSTAL CODE']  =  $order->shippingAddress->zip;
                    $row['STATE']  = $order->shippingAddress->state;
                    $row['SUBURB']  = $order->shippingAddress->city;
                    $row['TOTAL (RS.)']    = $order->order_total;

                    fputcsv($file, array($row['ORDER TRACKING NUMBER'], $row['STATUS'],  $row['INVENTORY STATUS'], $row['POSTAL CODE'], $row['STATE'], $row['SUBURB'], $row['TOTAL (RS.)']));
                }

                fclose($file);
            };
        } catch (\Exception $e) {

            $error = $e->getMessage() . ' - line - ' . $e->getLine();
            return view('admin.errors.error_500', compact('error'));
        }


        return response()->stream($callback, 200, $headers);
    }
}

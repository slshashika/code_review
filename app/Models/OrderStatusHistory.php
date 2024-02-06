<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = ['order_id','order_status_id','changed_by'];

    public static function saveOrderStatusHistory($orderId, $orderStatusId, $changedBy){

        $statusHistory = new OrderStatusHistory;

        $statusHistory->order_id = $orderId;
        $statusHistory->order_status_id = $orderStatusId;
        $statusHistory->changed_by = $changedBy;

        OrderStatusHistory::create($statusHistory->toArray());
    }
}

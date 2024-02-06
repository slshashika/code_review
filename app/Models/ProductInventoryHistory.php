<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInventoryHistory extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'operation', 'running_quantity', 'quantity', 'actual_reserved_quantity', 'order_id', 'processed_by', 'order_number', 'variant_id'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'processed_by', 'id');
    }

    
    public static function saveProductInventoryHistory($productId, $variantId, $operation, $quantity, $runningQuantity, $actualReservedQuantity, $processedBy, $orderId, $orderNumber)
    {


        $productInventoryHistory = new ProductInventoryHistory;

        $productInventoryHistory->operation = $operation;
        $productInventoryHistory->quantity = $quantity;
        $productInventoryHistory->running_quantity = $runningQuantity;
        $productInventoryHistory->product_id = $productId;
        $productInventoryHistory->variant_id = $variantId;
        $productInventoryHistory->actual_reserved_quantity = $actualReservedQuantity;
        $productInventoryHistory->processed_by = $processedBy;
        $productInventoryHistory->order_id = $orderId;
        $productInventoryHistory->order_number = $orderNumber;

        $savedHistory = ProductInventoryHistory::create($productInventoryHistory->toArray());
    }
}

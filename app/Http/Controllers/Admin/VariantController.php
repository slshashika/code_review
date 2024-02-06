<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Variant;
use App\Models\UserLog;
use App\Models\ProductInventory;
use App\Models\ProductInventoryHistory;
use Auth;

class VariantController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function index(Request $request)
    {

        $hasPermission = Auth::user()->hasPermission("view_variants");

        if ($hasPermission) {

            $selectedProduct = $request->selected_product;
            $searchKey = $request->searchKey;

            $products = Product::all();

            $product = null;
            $variants = array();

            if ($selectedProduct != null) {
                $product = Product::with('images', 'allVariants', 'featuredImage')->where('id', $selectedProduct)->get()->first();
                $variants = $product->allVariants()->where('variant_name', 'like', '%' . $searchKey . '%')->paginate(env("RECORDS_PER_PAGE"));
            }


            return view('admin.variants.product_variants', compact('products', 'variants', 'selectedProduct', 'searchKey', 'product'));
        } else {
            return redirect('admin/not_allowed');
        }
    }

    public function store(Request $request)
    {


        $hasPermission = Auth::user()->hasPermission("add_products");

        if ($hasPermission) {

            $this->validate(
                $request,
                [
                    "variant_name" => ["required", "unique:variants"],
                    "unit_price" => ["required"],
                    "selling_price" => ["required"],
                    "packing_cost" => ["required"],
                    "weight" => ["required"],
                ]
            );

            $variant = new Variant;

            $variant->product_id = $request->product_id;
            $variant->variant_name = $request->variant_name;
            $variant->description = $request->description;
            $variant->unit_price = $request->unit_price;
            $variant->packing_cost = $request->packing_cost;
            $variant->selling_price = $request->selling_price;
            $variant->weight = $request->weight;

            $savedVariant = Variant::create($variant->toArray());

            $productsVariants = Variant::where('product_id', $request->product_id)->orderBy('selling_price', 'asc')->get();

            Product::where('id', $request->product_id)->update([
                'selling_price' => $productsVariants[0]->selling_price
            ]);

            if (Auth::user()) {
                //saving user log
                UserLog::saveUserLog(Auth::user()->id, "New variant created", "Variant created with " . $variant->variant_name);
            }

            //creating product inventory
            $inventory = new ProductInventory();

            $inventory->product_id = $request->product_id;
            $inventory->variant_id = $savedVariant->id;
            $inventory->entered_by = Auth::user()->id;

            $inventory = ProductInventory::create($inventory->toArray());

            //saving product inventory history log
            ProductInventoryHistory::saveProductInventoryHistory($request->product_id, $savedVariant->id, "initial", 0, 0, 0, Auth::user()->id, null, null);

            return back()->with('success', 'Variant created successfully !');
        } else {
            return redirect('admin/not_allowed');
        }
        
    }


    public function update(Request $request)
    {


        $hasPermission = Auth::user()->hasPermission("edit_products");

        if ($hasPermission) {

            $this->validate(
                $request,
                [
                    "variant_name" => ['unique:variants,variant_name,' . $request->variant_id],
                    "unit_price" => ["required"],
                    "selling_price" => ["required"],
                    "packing_cost" => ["required"],
                    "weight" => ["required"],
                ]
            );

            $variant = Variant::where('id', $request->variant_id)->get()->first();

            if ($variant != null) {

                $variant->variant_name = $request->variant_name;
                $variant->description = $request->description;
                $variant->unit_price = $request->unit_price;
                $variant->packing_cost = $request->packing_cost;
                $variant->selling_price = $request->selling_price;
                $variant->weight = $request->weight;

                $variant->save();

                $productsVariants = Variant::where('product_id', $request->product_id)->orderBy('selling_price', 'asc')->get();

                Product::where('id', $request->product_id)->update([
                    'selling_price' => $productsVariants[0]->selling_price
                ]);

                if (Auth::user()) {
                    //saving user log
                    UserLog::saveUserLog(Auth::user()->id, "Variant updated", "Variant " . $variant->variant_name . " data updated");
                }

                return back()->with('success', 'Variant created successfully !');
            } else {

                return back()->with('error', 'Variant not found !');
            }
        } else {
            return redirect('admin/not_allowed');
        }
    }


    public function changeVariantStatus($id)
    {

        $hasPermission = Auth::user()->hasPermission("edit_products");

        if ($hasPermission) {
            try {
                $variant = Variant::where("id", $id)->get()->first();

                $msg = "";

                if ($variant->status == 0) {
                    $variant->status = 1;
                    $msg = "Variant activated successfully !";
                } else {
                    $variant->status = 0;
                    $msg = "Variant deactivated successfully !";
                }

                $variant->save();

                if (Auth::user()) {
                    //saving user log
                    UserLog::saveUserLog(Auth::user()->id, "Variant status changed", "Variant " . $variant->variant_name . " status changed to " . $variant->status);
                }

                return back()->with("success", $msg);
            } catch (\Exception $exception) {
                $error = $exception->getMessage();
                return view("admin.errors.error_500", compact("error"));
            }
        } else {
            return redirect("admin/not_allowed");
        }
    }
}

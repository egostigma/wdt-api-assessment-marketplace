<?php

namespace App\Http\Controllers\v1\Order;

use App\Exceptions\CustomException;
use App\Http\Controllers\ApiControllerV1;
use App\Models\Merchant\UserMerchantProduct;
use App\Models\Order\UserOrder;
use App\Models\Order\UserOrderProduct;
use Carbon\Carbon;

class OrderController extends ApiControllerV1
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $func = function () {
            $orders = UserOrder::where("user_id", auth()->user()->id)
                ->latest()
                ->paginate(request("perPage") ?? 10);

            foreach ($orders as $order) {
                $order->append(["created_at_formatted", "updated_at_formatted", "status_name"]);
            }

            $this->data = $orders;
        };

        return $this->callFunction($func);
    }

    public function store()
    {
        $func = function () {
            $this->validate(request(), [
                "product_id.*" => "exists:user_merchant_products,id",
                "cart_id.*" => "exists:user_carts,id",
            ]);

            $order = new UserOrder();
            $order->user_id = auth()->user()->id;
            $order->delivery_address = request("delivery_address") ?? auth()->user()->address;
            $order->phone = request("phone") ?? auth()->user()->phone;
            $order->note = request("note");
            $order->code = $this->generateOrderNum();
            $order->save();

            if (!!request("product_id")) {
                foreach (request("product_id") as $key => $product_id) {
                    $product = UserMerchantProduct::findOrFail($product_id);

                    if ($product->quantity >= request("quantity")[$key]) {
                        $order_product = new UserOrderProduct();
                        $order_product->user_order_id = $order->id;
                        $order_product->product_id = $product->id;
                        $order_product->price = $product->price;
                        $order_product->quantity = request("quantity")[$key];
                        $order_product->save();
                    }
                }
            }

            if (!!request("cart_id")) {
                throw_unless(
                    !!auth()
                        ->user()
                        ->carts()
                        ->whereIn("id", request("cart_id"))
                        ->count(),
                    new CustomException("Cart items not found", 422)
                );

                foreach (
                    auth()
                        ->user()
                        ->carts()
                        ->whereIn("id", request("cart_id"))
                        ->get()
                    as $cart
                ) {
                    $product = UserMerchantProduct::findOrFail($cart->product_id);

                    throw_unless(
                        $product->quantity >= $cart->quantity,
                        new CustomException("Product quantity are not enough", 422)
                    );

                    $order_product = new UserOrderProduct();
                    $order_product->user_order_id = $order->id;
                    $order_product->product_id = $product->id;
                    $order_product->price = $product->price;
                    $order_product->quantity = $cart->quantity;
                    $order_product->save();

                    $cart->delete();
                }
            }

            $user_order = UserOrder::with([
                "products" => function ($query) {
                    $query->select(
                        "id",
                        "user_order_id",
                        "product_id",
                        "price",
                        "quantity",
                        "created_at",
                        "updated_at"
                    );
                },
                "products.detail" => function ($query) {
                    $query->select("id", "user_merchant_id", "name", "slug", "description");
                },
                "products.detail.merchant" => function ($query) {
                    $query->select("id", "name", "slug");
                },
            ])->findOrFail($order->id);

            array_push($this->messages, "Checkout successful");

            $this->data = $user_order;
        };

        return $this->callFunction($func);
    }

    public function show($id)
    {
        $func = function () use ($id) {
            $order = UserOrder::with([
                "products" => function ($query) {
                    $query->select(
                        "id",
                        "user_order_id",
                        "product_id",
                        "price",
                        "quantity",
                        "created_at",
                        "updated_at"
                    );
                },
                "products.detail" => function ($query) {
                    $query->select("id", "user_merchant_id", "name", "slug", "description");
                },
                "products.detail.merchant" => function ($query) {
                    $query->select("id", "name", "slug");
                },
            ])
                ->where("user_id", auth()->user()->id)
                ->findOrFail($id);

            if (!!$order) {
                $order->append(["created_at_formatted", "updated_at_formatted", "status_name"]);
                foreach ($order->products as $product) {
                    $product->append(["created_at_formatted", "updated_at_formatted"]);
                }
            }

            $this->data = $order;
        };

        return $this->callFunction($func);
    }

    public function delete($id)
    {
        $func = function () use ($id) {
            $order = UserOrder::where("user_id", auth()->user()->id)
                ->where("status", UserOrder::UNCONFIRMED)
                ->findOrFail($id);

            $order->status = UserOrder::CANCELLED;
            $order->save();

            array_push($this->messages, "Order has been cancelled");
        };

        return $this->callFunction($func);
    }

    private function generateOrderNum()
    {
        $lastCode =
            UserOrder::whereDay("created_at", date("d"))
                ->whereMonth("created_at", date("m"))
                ->whereYear("created_at", date("Y"))
                ->orderBy("created_at", "DESC")
                ->first()->code ?? "";

        $lastSeq = (int) substr($lastCode, -5) ?? 0;
        $newSeq = $lastSeq + 1;

        return "WDT" . Carbon::now()->format("Ymd") . substr("00000{$newSeq}", -5);
    }
}

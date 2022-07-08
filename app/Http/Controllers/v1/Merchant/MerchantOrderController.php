<?php

namespace App\Http\Controllers\v1\Merchant;

use App\Http\Controllers\ApiControllerV1;
use App\Models\Merchant\UserMerchant;
use App\Models\Order\UserOrder;
use Illuminate\Support\Str;

class MerchantOrderController extends ApiControllerV1
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id)
    {
        $func = function () use ($id) {
            $orders = UserOrder::when(request("order") == "latest", function ($query) {
                $query->latest();
            })
                ->whereHas("products.detail.merchant", function ($query) use ($id) {
                    $query->where("id", $id);
                })
                ->whereIn("status", [0, 3])
                ->paginate(request("perPage") ?? 10);

            foreach ($orders as $order) {
                $order->append(["created_at_formatted", "updated_at_formatted", "status_name"]);
            }

            $this->data = $orders;
        };

        return $this->callFunction($func);
    }

    public function show($id, $order_id)
    {
        $func = function () use ($id, $order_id) {
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
                ->whereHas("products.detail.merchant", function ($query) use ($id) {
                    $query->where("id", $id);
                })
                ->whereIn("status", [0, 3])
                ->findOrFail($order_id);

            if (!!$order) {
                $order->append(["created_at_formatted", "updated_at_formatted", "status_name"]);
            }

            $this->data = $order;
        };

        return $this->callFunction($func);
    }

    public function update($id, $order_id)
    {
        $func = function () use ($id, $order_id) {
            $order = UserOrder::whereHas("products.detail.merchant", function ($query) use ($id) {
                $query->where("id", $id);
            })
                ->whereIn("status", [0])
                ->findOrFail($order_id);

            $order->status = UserOrder::CONFIRMED;
            $order->save();

            array_push($this->messages, "Order has been confirmed");
        };

        return $this->callFunction($func);
    }

    public function delete($id, $order_id)
    {
        $func = function () use ($id, $order_id) {
            $order = UserOrder::whereHas("products.detail.merchant", function ($query) use ($id) {
                $query->where("id", $id);
            })
                ->whereIn("status", [0])
                ->findOrFail($order_id);

            $order->status = UserOrder::REJECTED;
            $order->save();

            array_push($this->messages, "Order has been rejected");
        };

        return $this->callFunction($func);
    }
}

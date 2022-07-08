<?php

namespace App\Http\Controllers\v1\Product;

use App\Http\Controllers\ApiControllerV1;
use App\Models\Merchant\UserMerchantProduct;

class ProductController extends ApiControllerV1
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $func = function () {
            $products = UserMerchantProduct::when(request("query"), function ($query) {
                $query
                    ->where("name", "like", "%" . request("query") . "%")
                    ->where("description", "like", "%" . request("query") . "%");
            })
                ->when(request("merchant_id"), function ($query) {
                    $query->where("user_merchant_id", request("merchant_id"));
                })
                ->when(request("order") == "latest", function ($query) {
                    $query->latest();
                })
                ->otherMerchants()
                ->paginate(request("perPage") ?? 10);

            foreach ($products as $product) {
                $product->append(["created_at_formatted", "updated_at_formatted"]);
            }

            $this->data = $products;
        };

        return $this->callFunction($func);
    }

    public function show($id)
    {
        $func = function () use ($id) {
            $product = UserMerchantProduct::otherMerchants()->findOrFail($id);

            if (!!$product) {
                $product->append(["created_at_formatted", "updated_at_formatted"]);
            }

            $this->data = $product;
        };

        return $this->callFunction($func);
    }

    public function similar($id)
    {
        $func = function () use ($id) {
            $product = UserMerchantProduct::otherMerchants()
                ->inRandomOrder()
                ->where("id", "!=", $id)
                ->limit(3)
                ->get();

            if (!!$product) {
                $product->append(["created_at_formatted", "updated_at_formatted"]);
            }

            $this->data = $product;
        };

        return $this->callFunction($func);
    }
}

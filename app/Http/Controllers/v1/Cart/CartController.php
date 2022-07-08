<?php

namespace App\Http\Controllers\v1\Cart;

use App\Http\Controllers\ApiControllerV1;
use App\Models\Cart\UserCart;

class CartController extends ApiControllerV1
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $func = function () {
            $carts = UserCart::with([
                "product" => function ($query) {
                    $query->select("id", "user_merchant_id", "name", "slug", "price", "quantity");
                },
                "product.merchant" => function ($query) {
                    $query->select("id", "name", "slug");
                },
            ])
                ->where("user_id", auth()->user()->id)
                ->latest()
                ->paginate(request("perPage") ?? 10);

            foreach ($carts as $cart) {
                $cart->append(["created_at_formatted", "updated_at_formatted"]);
            }

            $this->data = $carts;
        };

        return $this->callFunction($func);
    }

    public function store()
    {
        $func = function () {
            $this->validate(request(), [
                "product_id" => "required|exists:user_merchant_products,id,deleted_at,NULL",
                "quantity" => "required|integer|min:1",
            ]);

            $cart = UserCart::where("product_id", request("product_id"))
                ->where("user_id", auth()->user()->id)
                ->first();

            if (!!$cart) {
                $cart->quantity += request("quantity");
                $cart->save();

                array_push($this->messages, "Product on your cart has been updated");
            } else {
                $cart = new UserCart();
                $cart->user_id = auth()->user()->id;
                $cart->product_id = request("product_id");
                $cart->quantity = request("quantity");
                $cart->save();

                array_push($this->messages, "Product has been added to your cart");
            }

            $this->data = $cart;
        };

        return $this->callFunction($func);
    }

    public function show($id)
    {
        $func = function () use ($id) {
            $product = UserCart::with([
                "product" => function ($query) {
                    $query->select("id", "user_merchant_id", "name", "slug", "price", "quantity");
                },
                "product.merchant" => function ($query) {
                    $query->select("id", "name", "slug");
                },
            ])
                ->where("user_id", auth()->user()->id)
                ->findOrFail($id);

            if (!!$product) {
                $product->append(["created_at_formatted", "updated_at_formatted"]);
            }

            $this->data = $product;
        };

        return $this->callFunction($func);
    }

    public function update($id)
    {
        $func = function () use ($id) {
            $cart = UserCart::with([
                "product" => function ($query) {
                    $query->select("id", "user_merchant_id", "name", "slug", "price", "quantity");
                },
                "product.merchant" => function ($query) {
                    $query->select("id", "name", "slug");
                },
            ])
                ->where("user_id", auth()->user()->id)
                ->findOrFail($id);

            $cart->quantity = request("quantity");
            $cart->save();

            if (!!$cart) {
                $cart->append(["created_at_formatted", "updated_at_formatted"]);
            }

            array_push($this->messages, "Product on your cart has been updated");

            $this->data = $cart;
        };

        return $this->callFunction($func);
    }

    public function delete($id)
    {
        $func = function () use ($id) {
            $cart = UserCart::where("user_id", auth()->user()->id)->findOrFail($id);

            $cart->delete();

            array_push($this->messages, "Product on your cart has been deleted");
        };

        return $this->callFunction($func);
    }
}

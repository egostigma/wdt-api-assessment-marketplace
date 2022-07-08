<?php

namespace App\Http\Controllers\v1\Merchant;

use App\Http\Controllers\ApiControllerV1;
use App\Models\Merchant\UserMerchantProduct;
use Illuminate\Support\Str;

class MerchantProductController extends ApiControllerV1
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id)
    {
        $func = function () use ($id) {
            $products = UserMerchantProduct::where(
                "user_merchant_id",
                auth()
                    ->user()
                    ->merchants()
                    ->findOrFail($id)->id
            )
                ->when(request("order") == "latest", function ($query) {
                    $query->latest();
                })
                ->paginate(request("perPage") ?? 10);

            foreach ($products as $product) {
                $product->append(["created_at_formatted", "updated_at_formatted"]);
            }

            $this->data = $products;
        };

        return $this->callFunction($func);
    }

    public function store($id)
    {
        $func = function () use ($id) {
            $this->validate(request(), [
                "name" => "required",
                "description" => "required",
                "price" => "required|integer|min:0",
                "quantity" => "required|integer|min:0",
            ]);

            $product = new UserMerchantProduct();
            $product->user_merchant_id = auth()
                ->user()
                ->merchants()
                ->findOrFail($id)->id;
            $product->name = request("name");
            $product->slug = Str::slug($product->name);
            if (
                !!UserMerchantProduct::withoutGlobalScopes()
                    ->where("slug", "like", "%$product->slug%")
                    ->count()
            ) {
                $product->slug .=
                    "-" .
                    UserMerchantProduct::withoutGlobalScopes()
                        ->where("slug", "like", "%$product->slug%")
                        ->count();
            }
            $product->description = request("description");
            $product->price = request("price");
            $product->quantity = request("quantity");
            $product->save();

            array_push($this->messages, "Product successfully created");

            $this->data = $product;
        };

        return $this->callFunction($func);
    }

    public function show($id, $product_id)
    {
        $func = function () use ($id, $product_id) {
            $product = auth()
                ->user()
                ->merchants()
                ->findOrFail($id)
                ->products()
                ->findOrFail($product_id);

            if (!!$product) {
                $product->append(["created_at_formatted", "updated_at_formatted"]);
            }

            $this->data = $product;
        };

        return $this->callFunction($func);
    }

    public function update($id, $product_id)
    {
        $func = function () use ($id, $product_id) {
            $this->validate(request(), [
                "name" => "required",
                "description" => "required",
                "price" => "required|integer|min:0",
                "quantity" => "required|integer|min:0",
            ]);

            $product = auth()
                ->user()
                ->merchants()
                ->findOrFail($id)
                ->products()
                ->findOrFail($product_id);

            $product->name = request("name");
            $product->slug = Str::slug($product->name);
            $product->description = request("description");
            $product->price = request("price");
            $product->quantity = request("quantity");
            $product->save();

            array_push($this->messages, "Product successfully updated");

            $this->data = $product;
        };

        return $this->callFunction($func);
    }

    public function delete($id, $product_id)
    {
        $func = function () use ($id, $product_id) {
            $product = auth()
                ->user()
                ->merchants()
                ->findOrFail($id)
                ->products()
                ->findOrFail($product_id)
                ->delete();

            array_push($this->messages, "Product successfully deleted");
        };

        return $this->callFunction($func);
    }
}

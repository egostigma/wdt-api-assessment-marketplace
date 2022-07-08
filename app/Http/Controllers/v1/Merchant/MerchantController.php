<?php

namespace App\Http\Controllers\v1\Merchant;

use App\Http\Controllers\ApiControllerV1;
use App\Models\Merchant\UserMerchant;
use Illuminate\Support\Str;

class MerchantController extends ApiControllerV1
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $func = function () {
            $merchants = UserMerchant::where("user_id", auth()->user()->id)->paginate(request("perPage") ?? 10);

            foreach ($merchants as $merchant) {
                $merchant->append("created_at_formatted");
            }

            $this->data = $merchants;
        };

        return $this->callFunction($func);
    }

    public function store()
    {
        $func = function () {
            $this->validate(request(), [
                "name" => "required",
                "address" => "required",
            ]);

            $merchant = new UserMerchant();
            $merchant->user_id = auth()->user()->id;
            $merchant->name = request("name");
            $merchant->slug = Str::slug($merchant->name);
            $merchant->address = request("address");
            $merchant->save();

            $this->data = $merchant;
        };

        return $this->callFunction($func);
    }

    public function show($id)
    {
        $func = function () use ($id) {
            $merchant = UserMerchant::where("user_id", auth()->user()->id)->findOrFail($id);

            if (!!$merchant) {
                $merchant->append("created_at_formatted");
            }

            $this->data = $merchant;
        };

        return $this->callFunction($func);
    }

    public function update($id)
    {
        $func = function () use ($id) {
            $this->validate(request(), [
                "name" => "required|unique:user_merchants,name," . $id,
                "slug" => "required|unique:user_merchants,slug," . $id,
                "address" => "required",
            ]);

            $merchant = UserMerchant::where("user_id", auth()->user()->id)->findOrFail($id);
            $merchant->name = request("name");
            $merchant->slug = request("slug");
            $merchant->address = request("address");
            $merchant->save();

            array_push($this->messages, "Merchant successfully updated");

            $this->data = compact("merchant");
        };

        return $this->callFunction($func);
    }

    public function delete($id)
    {
        $func = function () use ($id) {
            $merchant = UserMerchant::where("user_id", auth()->user()->id)->findOrFail($id);
            $merchant->delete();

            array_push($this->messages, "Merchant successfully deleted");
        };

        return $this->callFunction($func);
    }
}

<?php

namespace App\Http\Controllers\v1\Auth;

use App\Exceptions\CustomException;
use App\Http\Controllers\ApiControllerV1;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends ApiControllerV1
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $func = function () {
            $this->data = auth()->user();
        };

        return $this->callFunction($func);
    }

    public function register()
    {
        $func = function () {
            $this->validate(request(), [
                "name" => "required",
                "email" => "required|email",
                "password" => "required|confirmed",
            ]);

            $credentials = request()->only("email", "password");

            $input = request()->all();
            $input["password"] = Hash::make($input["password"]);
            $user = User::create($input);
            $access_token = $user->createToken("wdt")->accessToken;

            $this->data = compact("user", "access_token");
        };

        return $this->callFunction($func);
    }

    public function login()
    {
        $func = function () {
            $this->validate(request(), [
                "email" => "required|email",
                "password" => "required ",
            ]);

            $password_grant = DB::table("oauth_clients")
                ->select("id", "secret")
                ->where("provider", "users")
                ->latest()
                ->first();

            $response = Http::withHeaders([
                "Accept" => "application/json",
            ])->post(config("app.url") . ":" . env("APP_HTTP_PORT") . "/api/v1/oauth/token", [
                "grant_type" => "password",
                "client_id" => $password_grant->id,
                "client_secret" => $password_grant->secret,
                "username" => request("email"),
                "password" => request("password"),
                "scope" => request("scope") ?? "*",
            ]);

            $token = $response->json();

            throw_unless(!array_key_exists("error", $token), AuthenticationException::class);

            $user = User::where("email", request("email"))->first();

            $this->data = compact("user", "token");
        };

        return $this->callFunction($func);
    }
}

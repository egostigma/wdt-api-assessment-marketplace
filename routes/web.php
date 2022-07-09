<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get("/", function () use ($router) {
    return $router->app->version();
});


$router->group(["prefix" => "api", "as" => "api"], function () use ($router) {
    $router->group(["prefix" => "v1", "namespace" => "v1", "as" => "v1"], function () use ($router) {
        $router->group(["prefix" => "auth", "namespace" => "Auth", "as" => "auth"], function () use ($router) {
            $router->post("register", ["as" => "register", "uses" => "AuthController@register"]);
            $router->post("login", ["as" => "login", "uses" => "AuthController@login"]);

            $router->post("email/resend", ["as" => "verification.resend", "uses" => "VerificationController@resend"]);
            $router->post("password/reset-request", ["as" => "password.email", "uses" => "ForgotPasswordController@sendResetLinkEmail"]);
            $router->post("password/reset", [ "as" => "password.reset", "uses" => "ResetPasswordController@reset" ]);

            $router->group(["middleware" => "auth.api"], function () use ($router) {
                $router->get("/", ["as" => "index", "uses" => "AuthController@index"]);
                $router->put("/", ["as" => "update", "uses" => "AuthController@update"]);
            });
        });

        $router->group(["middleware" => "auth.api"], function () use ($router) {
            $router->group(["prefix" => "merchant", "namespace" => "Merchant", "as" => "merchant"], function () use ($router) {
                $router->get("/", ["as" => "index", "uses" => "MerchantController@index"]);
                $router->post("/", ["as" => "store", "uses" => "MerchantController@store"]);
                $router->get("/{id}", ["as" => "show", "uses" => "MerchantController@show"]);
                $router->put("/{id}", ["as" => "update", "uses" => "MerchantController@update"]);
                $router->delete("/{id}", ["as" => "delete", "uses" => "MerchantController@delete"]);

                $router->group(["prefix" => "{id}", "as" => "product"], function () use ($router) {
                    $router->get("/product", ["as" => "index", "uses" => "MerchantProductController@index"]);
                    $router->post("/product", ["as" => "store", "uses" => "MerchantProductController@store"]);
                    $router->get("/product/{product_id}", ["as" => "show", "uses" => "MerchantProductController@show"]);
                    $router->put("/product/{product_id}", ["as" => "update", "uses" => "MerchantProductController@update"]);
                    $router->delete("/product/{product_id}", ["as" => "delete", "uses" => "MerchantProductController@delete"]);
                });

                $router->group(["prefix" => "{id}", "as" => "order"], function () use ($router) {
                    $router->get("/order", ["as" => "index", "uses" => "MerchantOrderController@index"]);
                    $router->post("/order", ["as" => "store", "uses" => "MerchantOrderController@store"]);
                    $router->get("/order/{order_id}", ["as" => "show", "uses" => "MerchantOrderController@show"]);
                    $router->put("/order/{order_id}", ["as" => "update", "uses" => "MerchantOrderController@update"]);
                    $router->delete("/order/{order_id}", ["as" => "delete", "uses" => "MerchantOrderController@delete"]);
                });
            });

            $router->group(["prefix" => "cart", "namespace" => "Cart", "as" => "cart"], function () use ($router) {
                $router->get("/", ["as" => "index", "uses" => "CartController@index"]);
                $router->post("/", ["as" => "store", "uses" => "CartController@store"]);
                $router->get("/{id}", ["as" => "show", "uses" => "CartController@show"]);
                $router->put("/{id}", ["as" => "update", "uses" => "CartController@update"]);
                $router->delete("/{id}", ["as" => "delete", "uses" => "CartController@delete"]);
            });

            $router->group(["prefix" => "order", "namespace" => "Order", "as" => "order"], function () use ($router) {
                $router->get("/", ["as" => "index", "uses" => "OrderController@index"]);
                $router->post("/", ["as" => "store", "uses" => "OrderController@store"]);
                $router->get("/{id}", ["as" => "show", "uses" => "OrderController@show"]);
                $router->put("/{id}", ["as" => "update", "uses" => "OrderController@update"]);
                $router->delete("/{id}", ["as" => "delete", "uses" => "OrderController@delete"]);
            });
        });

        $router->group(["prefix" => "product", "namespace" => "Product", "as" => "product"], function () use ($router) {
            $router->get("/", ["as" => "index", "uses" => "ProductController@index"]);
            $router->get("/{id}", ["as" => "show", "uses" => "ProductController@show"]);
            $router->get("/{id}/similar", ["as" => "show", "uses" => "ProductController@similar"]);
        });
    });
});

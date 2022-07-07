<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

            $router->group(["middleware" => "auth.api"], function () use ($router) {
                $router->get("/", ["as" => "index", "uses" => "AuthController@index"]);
            });
        });
    });
});

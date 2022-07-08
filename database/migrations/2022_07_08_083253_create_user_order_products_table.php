<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_order_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_order_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->decimal('price', 18, 2)->default(0);
            $table->bigInteger('quantity')->default(0);
            $table->timestamps();

            $table->foreign('user_order_id')->references('id')->on('user_orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('user_merchant_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_order_products');
    }
}

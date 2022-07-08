<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMerchantProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_merchant_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->text('description');
            $table->decimal('price', 18, 2)->default(0);
            $table->bigInteger('quantity');
            $table->unsignedBigInteger('user_merchant_id')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_merchant_id')->references('id')->on('user_merchants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_merchant_products');
    }
}

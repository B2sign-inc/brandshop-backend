<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderProductAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_product_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_product_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('attribute_id')->unsigned();
            $table->string('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_product_attributes');
    }
}

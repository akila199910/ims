<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_adjusted_items', function (Blueprint $table) {
            $table->id();
            $table->integer('adjusted_id')->index('adjusted_id');
            $table->integer('product_id')->index('product_id');
            $table->integer('warehouse_id')->index('warehouse_id');
            $table->integer('order_item_id')->index('order_item_id');
            $table->integer('qty');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_adjusted_items');
    }
};

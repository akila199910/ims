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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no')->nullable();
            $table->integer('warehouse_from');
            $table->integer('warehouse_to');
            $table->string('transfer_date');
            $table->integer('created_by')->nullable();
            $table->integer('edit_by')->nullable();
            $table->integer('product_id')->index('product_id');
            $table->integer('business_id')->index('business_id');
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
        Schema::dropIfExists('stock_transfers');
    }
};

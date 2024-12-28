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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no')->nullable();
            $table->string('purchased_date');
            $table->integer('supplier_id')->index('supplier_id');
            $table->integer('business_id')->index('business_id');
            $table->string('status')->default(0)->comment('0 = Pending | 1 = Transferred | 2 = Returned');
            $table->integer('due_amount')->default(0)->nullable();
            $table->integer('final_amount')->default(0)->nullable();
            $table->integer('discount_amount')->default(0)->nullable();
            $table->integer('discount_percentage')->default(0)->nullable();
            $table->integer('total_amount')->default(0)->nullable();
            $table->string('order_by')->nullable();
            $table->string('modify_by')->nullable();
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
        Schema::dropIfExists('purchase_orders');
    }
};

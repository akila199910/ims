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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('ref_no')->nullable();
            $table->dropColumn('purchased_date');
            $table->dropColumn('supplier_id')->index('supplier_id');
            $table->dropColumn('business_id')->index('business_id');
            $table->dropColumn('status')->default(0)->comment('0 = Pending | 1 = Transferred | 2 = Returned');
            $table->dropColumn('due_amount')->default(0)->nullable();
            $table->dropColumn('final_amount')->default(0)->nullable();
            $table->dropColumn('discount_amount')->default(0)->nullable();
            $table->dropColumn('discount_percentage')->default(0)->nullable();
            $table->dropColumn('total_amount')->default(0)->nullable();
            $table->dropColumn('order_by')->nullable();
            $table->dropColumn('modify_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            //
        });
    }
};

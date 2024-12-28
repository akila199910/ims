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
            $table->string('ref_no')->nullable()->after('id');
            $table->date('purchased_date')->after('ref_no');
            $table->integer('supplier_id')->index('supplier_id')->after('purchased_date');
            $table->integer('business_id')->index('business_id')->after('supplier_id');
            $table->integer('status')->default(0)->comment('0 = Pending | 1 = Transferred | 2 = Returned')->after('business_id');
            $table->decimal('due_amount',20,2)->default(0)->nullable()->after('status');
            $table->decimal('final_amount',20,2)->default(0)->nullable()->after('due_amount');
            $table->decimal('discount_amount',20,2)->default(0)->nullable()->after('final_amount');
            $table->decimal('discount_percentage',20,2)->default(0)->nullable()->after('discount_amount');
            $table->decimal('total_amount',20,2)->default(0)->nullable()->after('discount_percentage');
            $table->integer('order_by')->index('order_by')->nullable()->after('total_amount');
            $table->integer('modify_by')->index('modify_by')->nullable()->after('order_by');
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

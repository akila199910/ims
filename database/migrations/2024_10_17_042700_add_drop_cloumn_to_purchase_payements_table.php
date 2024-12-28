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
        Schema::table('purchase_payements', function (Blueprint $table) {
            $table->dropColumn('payment_id');
            $table->integer('pay_type_id')->index('pay_type_id')->default(1)->after('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_payements', function (Blueprint $table) {
            //
        });
    }
};

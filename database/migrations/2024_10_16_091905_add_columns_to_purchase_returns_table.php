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
        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->decimal('sub_total_amount',20,2)->default(0)->after('return_date');
            $table->decimal('tax_amount',20,2)->default(0)->after('sub_total_amount');
            $table->decimal('shipping_amount',20,2)->default(0)->after('tax_amount');
            $table->decimal('other_amount',20,2)->default(0)->after('shipping_amount');
            $table->decimal('net_total_amount',20,2)->default(0)->after('other_amount');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            //
        });
    }
};

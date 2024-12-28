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
        Schema::table('purchase_return__items', function (Blueprint $table) {
            $table->decimal('unit_price',20,2)->default(0)->after('qty');
            $table->decimal('total_amount',20,2)->default(0)->after('unit_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_return__items', function (Blueprint $table) {
            //
        });
    }
};

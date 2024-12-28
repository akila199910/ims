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
        Schema::create('purchase_payements', function (Blueprint $table) {
            $table->id();
            $table->integer('purchased_id')->index('purchased_id');
            $table->decimal('paid_amount',20,2);
            $table->string('payment_reference')->nullable();
            $table->integer('payment_id')->nullable();
            $table->date('payment_date');
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
        Schema::dropIfExists('purchase_payements');
    }
};

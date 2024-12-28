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
        Schema::create('supplier_payment_infos', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no')->nullable();
            $table->integer('supplier_id')->index('supplier_id');
            $table->string('account_name');
            $table->string('bank_name');
            $table->string('branch_name');
            $table->string('account_number')->index('account_number');
            $table->integer('status')->default(1)->comment('0 = Inactive 1 = Active');
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
        Schema::dropIfExists('supplier_payment_infos');
    }
};

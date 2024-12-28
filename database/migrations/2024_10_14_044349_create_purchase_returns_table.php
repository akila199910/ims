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
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no')->nullable();
            $table->string('return_date');
            $table->string('status')->default(0)->comment('0 = PO_Return_Pending | 1 = PO_Return_Approval | 2 = PO_Return_OnHold | 3 = PO_Return_Canceled | 4 = PO_Return_FullFilled | 5 = PO_Return_Received | 6 = PO_Return_Closed');
            $table->integer('purchased_id')->index('purchased_id');
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
        Schema::dropIfExists('purchase_returns');
    }
};

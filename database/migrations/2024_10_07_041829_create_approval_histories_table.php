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
        Schema::create('approval_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->index('order_id');
            $table->integer('user_id')->index('user_id');
            $table->integer('status')->nullable()->comment('1 = Approved | 2 = On Hold | 3 = Canceled | 4 = Full Filled | 5 = Received | 6 = Closed');
            $table->timestamps();
            $table->softDeletes();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approval_histories');
    }
};

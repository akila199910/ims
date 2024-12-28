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
        Schema::create('writeoffs', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no')->nullable();
            $table->integer('qty');
            $table->string('reason')->nullable();
            $table->integer('product_id')->index('product_id');
            $table->integer('warehouse_id')->index('warehouse_id');
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
        Schema::dropIfExists('writeoffs');
    }
};

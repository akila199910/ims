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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no')->nullable();
            $table->integer('business_id');
            $table->string('name')->index('name');
            $table->integer('category_id')->index('category_id');
            $table->integer('subcategory_id')->index('subcategory_id');
            $table->integer('unit_id')->index('unit_id');
            $table->string('sort_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('qty')->default(0);
            $table->integer('status')->default(1)->comment('0 = Inactive 1 Active');
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
        Schema::dropIfExists('products');
    }
};

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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->integer('business_id')->index('business_id');
            $table->string('ref_no')->nullable();
            $table->string('name')->index('name');
            $table->string('email')->index('email');
            $table->string('contact')->index('contact');
            $table->integer('status')->default(1)->comment('0 = Inactive 1 = Active');
            $table->string('agree_doc')->nullable();
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
        Schema::dropIfExists('suppliers');
    }
};

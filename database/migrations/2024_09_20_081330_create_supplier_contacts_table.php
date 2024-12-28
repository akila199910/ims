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
        Schema::create('supplier_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no')->nullable();
            $table->integer('supplier_id')->index('supplier_id');
            $table->string('name')->index('name');
            $table->string('email')->index('email');
            $table->string('contact')->index('contact');
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
        Schema::dropIfExists('supplier_contacts');
    }
};

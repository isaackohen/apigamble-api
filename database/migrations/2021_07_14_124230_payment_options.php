<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaymentOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::create('PaymentOptions', function (Blueprint $table) {
            $table->id();
            $table->string('crypto');
            $table->string('token_forward_enabled');
            $table->string('token_forward_minimum');
            $table->string('token_forward_address');
            $table->string('cold_wallet');
            $table->string('gas_wallet');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('PaymentOptions');
    }
}






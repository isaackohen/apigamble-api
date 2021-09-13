<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaymentSecretkeysBitgo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('PaymentSecretkeysBitgo', function (Blueprint $table) {
            $table->id();
            $table->string('currency');
            $table->string('bitgo_currencyid');
            $table->string('bitgo_apikey');
            $table->string('bitgo_masterpass');
            $table->string('bitgo_walletid');
            $table->string('apikey');
            $table->string('callbackurl');
            $table->string('enabled');
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
        Schema::dropIfExists('PaymentSecretkeysBitgo');
    }
}


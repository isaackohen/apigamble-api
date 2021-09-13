<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaymentTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('PaymentTransactions', function (Blueprint $table) {
            $table->id();
            $table->string('from');
            $table->string('to');
            $table->string('amount');
            $table->string('amountusd');
            $table->string('txid');
            $table->string('currency');
            $table->string('apikey');
            $table->string('callbackurl');
            $table->string('subscribed');
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
        Schema::dropIfExists('PaymentTransactions');
    }
}






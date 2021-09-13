<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Wallets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Wallets', function (Blueprint $table) {
            $table->id();
            $table->string('wallet');
            $table->string('label');
            $table->string('currency');
            $table->string('balance');
            $table->string('tokenbalance');
            $table->string('apikey');
            $table->string('callbackurl');
            $table->string('subscribed');
            $table->string('contractaddress');
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
        Schema::dropIfExists('Wallets');
    }
}


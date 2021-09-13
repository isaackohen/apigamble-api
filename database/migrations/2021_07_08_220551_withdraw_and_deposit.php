<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WithdrawAndDeposit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawAndDeposit', function (Blueprint $table) {
            $table->id();
            $table->string('operator');
            $table->string('user');
            $table->string('withdraw');
            $table->string('deposit');
            $table->string('currency');
            $table->string('gameid');
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
        Schema::dropIfExists('withdrawAndDeposit');
    }
}

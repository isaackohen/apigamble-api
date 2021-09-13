<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CallbackQueue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::create('CallbackQueue', function (Blueprint $table) {
            $table->id();
            $table->string('internal_id');
            $table->string('external_id');
            $table->string('action');
            $table->string('timestamp');
            $table->string('currency');
            $table->string('type');
            $table->string('address');
            $table->string('amount');
            $table->string('callbackurl');
            $table->string('queue_state');
            $table->string('queue_tries');
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
        Schema::dropIfExists('CallbackQueue');
    }
}






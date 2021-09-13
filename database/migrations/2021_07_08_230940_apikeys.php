<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Apikeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Apikeys', function (Blueprint $table) {
            $table->id();
            $table->string('apikey');
            $table->string('operator');
            $table->string('bankgroup');
            $table->string('bankgroupeur');
            $table->string('bonusbankgroup');
            $table->string('bonusbankgroupeur');
            $table->string('callbackurl');
            $table->string('statichost');
            $table->string('sessiondomain');
            $table->string('ownedBy');
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
        Schema::dropIfExists('Apikeys');
    }
}


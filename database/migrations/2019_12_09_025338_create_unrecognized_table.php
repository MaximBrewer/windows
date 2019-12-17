<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnrecognizedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unrecognizeds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('car_model', 255);
            $table->integer('car_producer_id');
            $table->string('eurocode', 255);
            $table->string('misstake', 50);
            $table->timestamps();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unrecognizeds');
    }
}

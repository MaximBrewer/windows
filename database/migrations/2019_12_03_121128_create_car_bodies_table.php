<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarBodiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_bodies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 255)->index();
            $table->integer('car_model_id')->index();
            $table->integer('body_type_id')->index();
            $table->string('eurocode');
            $table->integer('doors');
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
        Schema::dropIfExists('car_bodies');
    }
}

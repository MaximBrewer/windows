<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWindowModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('window_models', function (Blueprint $table) {
            $table->bigIncrements('id', 11);
            $table->string('title', 255)->index();
            $table->integer('window_producer_id')->index();
            $table->integer('car_model_id')->index();
            $table->integer('car_body_id')->index();
            $table->integer('window_type_id')->index();
            $table->string('eurocode', 255)->index();
            $table->string('type', 255);
            $table->string('tushino', 255);
            $table->string('kuncevo', 255);
            $table->string('marino', 255);
            $table->string('ismailovo', 255);
            $table->string('mkad32km', 255);
            $table->string('medvedkovo', 255);
            $table->string('price_install', 255);
            $table->string('price_opt', 255);
            $table->integer('provider');
            $table->string('quantity', 255)->index();
            $table->string('size', 255);
            $table->string('time', 255);
            $table->string('year', 255);
            $table->string('spec', 255);
            $table->string('char', 255);
            $table->string('stock', 255);
            $table->string('skolkovo', 255);
            $table->string('lipeckaya', 255);
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
        Schema::dropIfExists('window_models');
    }
}

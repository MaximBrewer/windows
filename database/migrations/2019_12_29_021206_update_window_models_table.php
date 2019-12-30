<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWindowModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('window_models', function (Blueprint $table) {
            $table->integer('car_producer_id')->index()->after('window_producer_id');
        });

        DB::update('update `window_models` as `wm` set `car_producer_id` = (select `car_producer_id` from `car_models` where `id` = wm.car_model_id limit 1)', []);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

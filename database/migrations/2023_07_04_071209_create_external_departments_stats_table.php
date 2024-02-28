<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExternalDepartmentsStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_departments_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dep_id');
            $table->integer('total');
            $table->integer('present');
            $table->integer('absent');
            $table->integer('late');
            $table->integer('onleave');
            $table->integer('ontour');
            $table->integer('onschedule');
            $table->integer('onattach');
            $table->integer('shift_not_started');
            $table->integer('early_exit');
            $table->integer('off_days');
            $table->date('updated_date');
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
        Schema::dropIfExists('external_departments_stats');
    }
}

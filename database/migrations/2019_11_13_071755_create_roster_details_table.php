<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRosterDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roster_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('tcat_id');
            $table->integer('roster_id');
            $table->date('tcat_from_date');
            $table->date('tcat_to_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roster_details');
    }
}

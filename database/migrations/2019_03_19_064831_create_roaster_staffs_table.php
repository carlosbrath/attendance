<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoasterStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roaster_staffs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('month');
            $table->integer("user_id")->unsigned();
            $table->foreign("user_id")->references("id")->on("users");
            $table->integer("tcat_id")->unsigned();
            $table->foreign("tcat_id")->references("id")->on("time_categories");
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
        Schema::dropIfExists('roaster_staffs');
    }
}

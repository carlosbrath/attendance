<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleRightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_rights', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("role_id")->unsigned();
            $table->foreign("role_id")->references("id")->on("roles");
            $table->integer("module_id")->unsigned();
            $table->foreign("module_id")->references("id")->on("modules");
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
        Schema::dropIfExists('module_rights');
    }
}

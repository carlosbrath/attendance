<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClientTrees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('client_trees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('children_ids');
            $table->string('all_children_ids');
            $table->string('child_label_json');
            $table->string('tree_jason');
            $table->string('key_values');
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
        //
    }
}

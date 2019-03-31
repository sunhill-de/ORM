<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExternalhooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('externalhooks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('container_id');
            $table->integer('target_id');
            $table->string('action');
            $table->string('subaction');
            $table->string('hook');
            $table->string('payload');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('externalhooks');
    }
}

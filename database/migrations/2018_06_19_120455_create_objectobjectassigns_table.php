<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateObjectobjectassignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('objectobjectassigns', function (Blueprint $table) {
            $table->integer('container_id');
            $table->integer('element_id');
            $table->string('field',50);
            $table->integer('index');
            $table->primary(['container_id','element_id','field']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('objectobjectassigns');
    }
}

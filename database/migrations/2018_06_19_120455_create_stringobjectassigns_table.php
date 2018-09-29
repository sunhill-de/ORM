<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStringobjectassignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stringobjectassigns', function (Blueprint $table) {
            $table->integer('container_id');
            $table->string('element_id');
            $table->string('field');
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
        Schema::dropIfExists('stringobjectassigns');
    }
}

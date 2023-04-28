<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateObjectsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('objects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('classname');
            $table->string('uuid',40);
            $table->integer('obj_owner')->default(0);
            $table->integer('obj_group')->default(0);
            $table->integer('obj_read')->unsigned()->default(7);
            $table->integer('obj_edit')->unsigned()->default(7);
            $table->integer('obj_delete')->unsigned()->default(7);
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
        Schema::dropIfExists('objects');
    }
}

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
            $table->string('_uuid',40);
            $table->integer('_owner')->default(0);
            $table->integer('_group')->default(0);
            $table->integer('_read')->unsigned()->default(7);
            $table->integer('_edit')->unsigned()->default(7);
            $table->integer('_delete')->unsigned()->default(7);
            $table->datetime('_created_at')->nullable()->default(null);
            $table->datetime('_updated_at')->nullable()->default(null);
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

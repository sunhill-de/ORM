<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestParentsArrayParentOArrayTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testparents_array_parentoarray', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('value');
            $table->integer('index');
            $table->primary(['id','index']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('testparents_array_parentoarray');
    }
}

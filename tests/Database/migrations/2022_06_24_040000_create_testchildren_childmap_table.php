<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestchildrenChildMapTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testchildren_childmap', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('value');
            $table->char('index',20);
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
        Schema::dropIfExists('testchildren_childmap');
    }
}

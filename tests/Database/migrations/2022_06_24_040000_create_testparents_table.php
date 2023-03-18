<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestParentsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testparents', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('parentint');
            $table->char('parentchar')->nullable();
            $table->float('parentfloat');
            $table->text('parenttext');
            $table->datetime('parentdatetime');
            $table->date('parentdate');
            $table->time('parenttime');
            $table->enum('parentenum',['testA','testB','testC']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('testparents');
    }
}

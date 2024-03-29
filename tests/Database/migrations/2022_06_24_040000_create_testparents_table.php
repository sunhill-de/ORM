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
            $table->char('parentchar',10)->nullable();
            $table->boolean('parentbool');
            $table->float('parentfloat');
            $table->text('parenttext');
            $table->datetime('parentdatetime');
            $table->date('parentdate');
            $table->time('parenttime');
            $table->enum('parentenum',['testA','testB','testC']);
            $table->integer('parentobject')->nullable()->default(null);
            $table->integer('nosearch')->nullable(0)->default(1);
            $table->string('parentcalc');
            $table->integer('parentcollection')->nullable()->default(null);
            $table->string('parentinformation')->nullable()->default(null);
            $table->primary('id');
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

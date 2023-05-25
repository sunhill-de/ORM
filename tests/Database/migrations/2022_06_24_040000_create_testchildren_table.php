<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestChildrenTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testchildren', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('childint');
            $table->char('childchar')->nullable();
            $table->float('childfloat');
            $table->text('childtext');
            $table->datetime('childdatetime');
            $table->date('childdate');
            $table->time('childtime');
            $table->integer('childobject')->nullable()->default(null);
            $table->enum('childenum',['testA','testB','testC']);
            $table->string('childcalc');
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
        Schema::dropIfExists('testchildren');
    }
}

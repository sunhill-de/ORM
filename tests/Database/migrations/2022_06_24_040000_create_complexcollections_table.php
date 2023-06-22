<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComplexCollectionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complexcollections', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('field_int');
            $table->char('field_char',10)->nullable();
            $table->float('field_float');
            $table->text('field_text');
            $table->datetime('field_datetime');
            $table->date('field_date');
            $table->time('field_time');
            $table->enum('field_enum',['testA','testB','testC']);
            $table->integer('field_object')->nullable()->default(null);
            $table->integer('field_collection')->nullable()->default(null);
            $table->integer('nosearch')->nullable(0)->default(1);
            $table->string('field_calc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complexcollections');
    }
}

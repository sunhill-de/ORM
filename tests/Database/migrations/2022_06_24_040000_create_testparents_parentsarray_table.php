<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestparentsParentSArrayTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testparents_parentsarray', function (Blueprint $table) {
            $table->integer('id');
            $table->char('value');
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
        Schema::dropIfExists('testparents_parentsarray');
    }
}

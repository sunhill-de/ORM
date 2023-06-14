<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestparentsParentSMapTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testparents_parentmap', function (Blueprint $table) {
            $table->integer('id');
            $table->char('value');
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
        Schema::dropIfExists('testparents_parentmap');
    }
}

<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThirdlevelChildrenTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thirdlevelchildren', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('childchildint');
            $table->char('childchildchar')->nullable();
            $table->integer('thirdlevelobject')->nullable()->setDefault(null);
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

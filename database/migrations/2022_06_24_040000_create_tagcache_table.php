<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagcacheTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tagcache', function (Blueprint $table) {
            $table->string('path_name',150);
            $table->integer('tag_id');
            $table->boolean('is_fullpath')->default(false);
            $table->timestamps();
            // $table->primary('id');
            $table->primary(['path_name','tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tagcache');
    }
}

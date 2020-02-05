<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateH5PBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h5p_blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->text('json_content')->nullable();
            $table->text('filtered')->nullable();
            $table->string('embed_type')->default('div');
            $table->string('slug')->nullable();
            $table->integer('main_library_id')->nullable();
            $table->integer('disable')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('h5p_blocks');
    }
}

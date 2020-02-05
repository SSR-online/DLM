<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImageBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('path')->nullable();
            $table->string('alt')->nullable();
            $table->text('longdesc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('image_blocks');
    }
}

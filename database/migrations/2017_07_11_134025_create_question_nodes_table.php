<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_blocks', function (Blueprint $table) {
            // Node fields
            $table->increments('id');
            $table->timestamps();

            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->mediumtext('content')->nullable();

            // subclass fields
            $table->string('question_type')->nullable();
            $table->mediumtext('question')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_blocks');
    }
}

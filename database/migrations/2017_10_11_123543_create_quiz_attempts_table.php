<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('quiz_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('last_question')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_attempts');
    }
}

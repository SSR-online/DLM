<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuizIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_attempts', function (Blueprint $table) {
            $table->index(['user_id', 'question_id', 'quiz_attempt_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('question_attempts', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'question_id', 'quiz_attempt_id']);
        });
    }
}
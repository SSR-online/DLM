<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditQuestionQuizAddFeedback extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_blocks', function (Blueprint $table) {
            $table->text('feedback_correct')->nullable();
            $table->text('feedback_incorrect')->nullable();
        });

        Schema::table('question_blocks', function (Blueprint $table) {
            $table->text('feedback_correct')->nullable();
            $table->text('feedback_incorrect')->nullable();
        });

        Schema::table('answer_options', function (Blueprint $table) {
            $table->text('feedback_correct')->nullable();
            $table->text('feedback_incorrect')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quiz_blocks', function (Blueprint $table) {
            $table->dropColumn('feedback_correct');
            $table->dropColumn('feedback_incorrect');
        });

        Schema::table('question_blocks', function (Blueprint $table) {
            $table->dropColumn('feedback_correct');
            $table->dropColumn('feedback_incorrect');
        });

        Schema::table('answer_options', function (Blueprint $table) {
            $table->dropColumn('feedback_correct');
            $table->dropColumn('feedback_incorrect');
        });
    }
}

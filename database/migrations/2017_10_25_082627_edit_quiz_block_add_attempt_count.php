<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditQuizBlockAddAttemptCount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_blocks', function (Blueprint $table) {
            $table->integer('attempts_allowed')->default(1);
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
            $table->dropColumn('attempts_allowed');
        });
    }
}

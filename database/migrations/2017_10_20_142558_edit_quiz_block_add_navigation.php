<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditQuizBlockAddNavigation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_blocks', function (Blueprint $table) {
            $table->boolean('allow_navigation')->default(true);
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
            $table->dropColumn('allow_navigation');
        });
    }
}

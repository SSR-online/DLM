<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditTextblockAddSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('text_blocks', function (Blueprint $table) {
            $table->text('settings')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
         Schema::table('text_blocks', function (Blueprint $table) {
            $table->dropColumn('settings');
        });
    }
}

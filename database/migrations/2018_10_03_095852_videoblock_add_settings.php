<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VideoblockAddSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_blocks', function (Blueprint $table) {
            $table->text('settings')->nullable();
            $table->text('caption')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_blocks', function (Blueprint $table) {
            $table->dropColumn('settings');
            $table->dropColumn('caption');
        });
    }
}
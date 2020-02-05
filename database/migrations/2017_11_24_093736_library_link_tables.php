<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LibraryLinkTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h5p_library_libraries', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('library_id')->unsigned();
            $table->integer('required_library_id')->unsigned();
            $table->string('dependency_type');
        });

        Schema::table('h5p_blocks_libraries', function (Blueprint $table) {
            $table->string('dependency_type');
            $table->boolean('drop_css');
            $table->integer('weight');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('h5p_library_libraries');
        Schema::table('h5p_blocks_libraries', function (Blueprint $table) {
            $table->dropColumn('dependency_type');
            $table->dropColumn('drop_css');
            $table->dropColumn('weight');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateH5PLibrariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h5p_libraries', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('machine_name');
            $table->string('title');
            $table->integer('major_version');
            $table->integer('minor_version');
            $table->integer('patch_version');
            $table->boolean('runnable');
            $table->boolean('fullscreen');
            $table->longtext('semantics');
            $table->string('embed_types');
            $table->longtext('preloaded_css');
            $table->longtext('preloaded_js');
            $table->longtext('drop_library_css');
            $table->boolean('restricted')->default(0);
            $table->string('tutorial_url')->nullable();
            $table->boolean('has_icon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('h5p_libraries');
    }
}

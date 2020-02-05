<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nodes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->mediumtext('description')->nullable();

            //Node types
            $table->string('block_type')->nullable();
            $table->integer('block_id')->unsigned()->nullable();
            
            $table->integer('module_id');
            $table->integer('parent_id')->nullable();
            $table->integer('previous_id')->nullable();
            $table->integer('next_id')->nullable();
            $table->integer('sort_order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nodes');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('layouts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('node_id');
            $table->timestamps();
            $table->string('title')->nullable();
            $table->string('type');
        });

        Schema::create('layout_slots', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('layout_id');
            $table->string('title')->nullable();
        });

        Schema::table('nodes', function (Blueprint $table) {
            $table->integer('layout_slot_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('layouts');
        Schema::dropIfExists('layout_slots');

        Schema::table('nodes', function (Blueprint $table) {
            $table->dropColumn('layout_slots_id');
        });
    }
}

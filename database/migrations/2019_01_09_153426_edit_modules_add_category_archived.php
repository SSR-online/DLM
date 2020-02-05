<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditModulesAddCategoryArchived extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
       Schema::table('modules', function (Blueprint $table) {
            $table->boolean('archived')->nullable();
            $table->string('category')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('archived');
            $table->dropColumn('category');
        });
    }
}

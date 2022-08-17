<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TagsChangeApiId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('tags', function (Blueprint $table) {
		    $table->string('get_your_guide_id', 255)->change();
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('tags', function (Blueprint $table) {
		    $table->integer('get_your_guide_id')->change();
	    });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LocationsAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('locations', function (Blueprint $table) {
		    $table->string('title', 512)->nullable();
		    $table->string('title_bottom', 512)->nullable();
		    $table->text('meta_description')->nullable();
		    $table->string('changed_fields', 512)->nullable();
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('locations', function (Blueprint $table) {
		    $table->dropColumn('title');
		    $table->dropColumn('title_bottom');
		    $table->dropColumn('meta_description');
		    $table->dropColumn('changed_fields');
	    });
    }
}

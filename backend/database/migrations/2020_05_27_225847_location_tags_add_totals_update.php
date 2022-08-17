<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LocationTagsAddTotalsUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    //Schema::table('location_tags', function (Blueprint $table) {
		//    $table->dropColumn('total_tours');
	    //});
	    Schema::table('location_tags', function (Blueprint $table) {
		    $table->unique(['lang', 'location_api_id', 'tag_slug_id']);
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('location_tags', function (Blueprint $table) {
		    $table->integer('total_tours')->nullable();
		    $table->renameColumn('location_api_id', 'location_id');
	    });
    }
}

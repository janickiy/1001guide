<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LocationTagsAddTotalsAgain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		Schema::table('location_tags', function (Blueprint $table) {
			$table->integer('total_tours')->nullable();
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
			$table->dropColumn('total_tours');
		});
	}
}

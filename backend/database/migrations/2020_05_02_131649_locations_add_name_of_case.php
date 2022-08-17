<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LocationsAddNameOfCase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('locations', function (Blueprint $table) {
		    $table->string('name_of_case', 255)->nullable();
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
		    $table->dropColumn('name_of_case');
	    });
    }
}

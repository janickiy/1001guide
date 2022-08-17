<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LocationsAddTextAndBg extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('locations', function (Blueprint $table) {
		    $table->string('bg', 255)->nullable();
		    $table->text('announce')->nullable();
		    $table->text('content')->nullable();
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
		    $table->dropColumn('bg');
		    $table->dropColumn('announce');
		    $table->dropColumn('content');
	    });
    }
}

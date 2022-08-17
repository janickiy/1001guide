<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CountriesAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('countries', function (Blueprint $table) {
		    $table->string('name_of_case', 255)->nullable();
		    $table->string('title', 512)->nullable();
		    $table->string('title_bottom', 512)->nullable();
		    $table->text('announce')->nullable();
		    $table->text('content')->nullable();
		    $table->text('meta_description')->nullable();
		    $table->string('bg', 255)->nullable();
		    $table->integer('total')->nullable();
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
	    Schema::table('countries', function (Blueprint $table) {
		    $table->dropColumn('name_of_case');
		    $table->dropColumn('title');
		    $table->dropColumn('title_bottom');
		    $table->dropColumn('announce');
		    $table->dropColumn('content');
		    $table->dropColumn('meta_description');
		    $table->dropColumn('bg');
		    $table->dropColumn('total');
		    $table->dropColumn('changed_fields');
	    });
    }
}

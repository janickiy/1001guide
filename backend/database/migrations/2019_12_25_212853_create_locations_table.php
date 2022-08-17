<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('api_id');
	        $table->integer('api_parent_id')->nullable();
            $table->string('lang', 8);
            $table->string('name', 255);
	        $table->string('name_in_case', 255)->nullable();
	        $table->string('type', 255)->nullable();
	        $table->string('country_code', 8);
	        $table->float('lat')->nullable();
	        $table->float('long')->nullable();
            $table->timestamps();
	        $table->unique(['name', 'lang']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GenerationStatusesReform extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('generation_statuses', function (Blueprint $table) {
		    $table->dropColumn('is_finished');
		    $table->string('status', 255)->nullable();
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('generation_statuses', function (Blueprint $table) {
		    $table->boolean('is_finished')->default(1);
		    $table->dropColumn('status');
	    });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TemplateFieldValuesUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('template_field_values', function (Blueprint $table) {
		    $table->dropColumn('field_id');
		    $table->dropColumn('order');
		    $table->string('page_type', 255);
		    $table->string('field', 255);
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('template_field_values', function (Blueprint $table) {
		    $table->bigInteger('field_id');
		    $table->integer('order');
		    $table->dropColumn('page_type');
		    $table->dropColumn('field');
	    });
    }
}

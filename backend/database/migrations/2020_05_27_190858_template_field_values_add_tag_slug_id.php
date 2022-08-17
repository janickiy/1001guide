<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TemplateFieldValuesAddTagSlugId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('template_field_values', function (Blueprint $table) {
		    $table->integer('tag_slug_id')->nullable();
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
		    $table->dropColumn('tag_slug_id');
	    });
    }
}

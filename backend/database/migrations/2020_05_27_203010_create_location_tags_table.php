<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lang', 8);
            $table->bigInteger('location_id');
            $table->bigInteger('tag_slug_id');
	        $table->string('title', 512)->nullable();
	        $table->string('title_bottom', 512)->nullable();
	        $table->text('announce')->nullable();
	        $table->text('content')->nullable();
	        $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->unique(['lang', 'location_id', 'tag_slug_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_tags');
    }
}

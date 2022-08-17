<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('get_your_guide_id');
	        $table->string('lang', 8);
	        $table->string('name', 255);
	        $table->string('title', 512)->nullable();
	        $table->string('title_bottom', 512)->nullable();
	        $table->text('announce')->nullable();
	        $table->text('content')->nullable();
	        $table->text('meta_description')->nullable();
	        $table->string('changed_fields', 512)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}

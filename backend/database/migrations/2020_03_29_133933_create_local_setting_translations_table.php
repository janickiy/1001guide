<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalSettingTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_setting_translations', function (Blueprint $table) {
	        $table->bigIncrements('id');
	        $table->bigInteger('setting_id');
	        $table->string('lang', 8);
	        $table->text('value')->nullable();
	        $table->timestamps();
	        $table->unique(['setting_id', 'lang']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('local_setting_translations');
    }
}

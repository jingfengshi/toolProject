<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameFakersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_fakers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('appletId');
            $table->text('ori_banner_images');
            $table->text('ori_content_images');
            $table->text('banner_images');
            $table->text('content_images');
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
        Schema::dropIfExists('game_fakers');
    }
}

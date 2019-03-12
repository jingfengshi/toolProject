<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameBannerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_banner', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url', 100)->nullable(false);
            $table->string('jumpId', 50)->nullable(false);
            $table->string('ghId', 50)->nullable(false);
            $table->string('jumpUrl', 100)->nullable();
            $table->string('jumpAppId', 100)->nullable(false);
            $table->string('bannerUrl', 100)->nullable(false);
            $table->text('introduce')->nullable();
            $table->string('jumpType')->nullable(false);
            $table->string('jumpName', 100)->nullable(false);
            $table->string('display')->nullable(false);
            $table->string('extraData', 200)->nullable();
            $table->string('clickNub', 20)->nullable(false);
            $table->string('logo', 100)->nullable(false);
            $table->string('clickRate', 20)->nullable(false);
            $table->string('tabLogo', 100)->nullable();
            $table->string('sort', 100)->nullable();
            $table->string('jumpGhId', 100)->nullable(false);
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
        Schema::dropIfExists('game_banner');
    }
}

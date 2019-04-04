<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_content', function (Blueprint $table) {
            $table->increments('id');
            $table->string('appid', 50)->nullable(false);
            $table->string('alias', 30)->nullable(true);
            $table->string('domain', 100)->nullable(true);
            $table->string('url', 150)->nullable(true);
            $table->string('imgUrl', 100)->nullable(true);
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
        Schema::dropIfExists('wechat_content');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatUserPortraitPlatformsuvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_user_portrait_platformsuv', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gh_id', 40)->comment('小程序id');
            $table->string('ref_date',17)->comment('日期');
            $table->integer('iphone')->default(0);
            $table->integer('android')->default(0);
            $table->integer('other')->default(0);
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
        Schema::dropIfExists('wechat_user_portrait_platformsuv');
    }
}

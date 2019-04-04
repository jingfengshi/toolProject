<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatUserPortraitGenderuvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_user_portrait_genderuv', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gh_id', 40)->comment('小程序id');
            $table->string('ref_date',17)->comment('日期');
            $table->integer('male')->default(0);
            $table->integer('female')->default(0);
            $table->integer('unknown')->default(0);
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
        Schema::dropIfExists('wechat_user_portrait_genderuv');
    }
}

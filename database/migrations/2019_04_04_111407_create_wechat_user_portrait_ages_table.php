<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatUserPortraitAgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_user_portrait_ages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gh_id', 40)->comment('小程序id');
            $table->string('ref_date',17)->comment('日期');
            $table->integer('unknown')->default(0);
            $table->integer('under17')->default(0);
            $table->integer('age18_24')->default(0);
            $table->integer('age25_29')->default(0);
            $table->integer('age30_39')->default(0);
            $table->integer('age40_49')->default(0);
            $table->integer('over50')->default(0);
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
        Schema::dropIfExists('wechat_user_portrait_ages');
    }
}

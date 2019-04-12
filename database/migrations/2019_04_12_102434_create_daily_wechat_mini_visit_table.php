<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyWechatMiniVisitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_wechat_mini_visit', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gh_id', 40)->comment('小程序id');
            $table->string('ref_date',8)->comment('日期');
            $table->integer('enter_times')->default(0);
            $table->integer('reply_times')->default(0);
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
        Schema::dropIfExists('daily_wechat_mini_visit');
    }
}

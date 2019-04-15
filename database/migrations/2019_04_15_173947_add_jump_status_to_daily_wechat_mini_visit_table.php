<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJumpStatusToDailyWechatMiniVisitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_wechat_mini_visit', function (Blueprint $table) {
            $table->integer('jump_success')->default(0);
            $table->integer('jump_fail')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_wechat_mini_visit', function (Blueprint $table) {
            //
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAppsecretAeskeyTokenToWechatAppletTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wechat_applet', function (Blueprint $table) {
            $table->string('appsecret', 40)->nullable(false);
            $table->string('aeskey', 50)->nullable(false);
            $table->string('token', 30)->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wechat_applet', function (Blueprint $table) {
            //
        });
    }
}

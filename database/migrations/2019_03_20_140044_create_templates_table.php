<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('模板标题');
            $table->string('bg_width')->comment('背景图宽度');
            $table->string('bg_height')->comment('背景图高度');
            $table->string('code_start_x')->comment('二维码开始x坐标');
            $table->string('code_start_y')->comment('二维码开始y坐标');
            $table->string('code_end_x')->comment('二维码结束x坐标');
            $table->string('code_end_y')->comment('二维码结束y坐标');
            $table->string('code_width')->comment('二维码宽度');
            $table->string('code_height')->comment('二维码高度');
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
        Schema::dropIfExists('templates');
    }
}

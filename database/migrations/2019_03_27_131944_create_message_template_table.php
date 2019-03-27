<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gh_id', 40)->nullable(false);
            $table->string('type', 30)->nullable(false);
            $table->string('content', 255)->nullable(true)->comment('文本消息使用');
            $table->string('media_id', '60')->nullable(true)->comment('图片消息和卡片消息封面使用');
            $table->string('title', '50')->nullable(true)->comment('图文和卡片消息使用');
            $table->string('description', 255)->nullable(true)->comment('图文消息使用');
            $table->string('url', 150)->nullable(true)->comment('图文消息使用');
            $table->string('thumb_url', 150)->nullable(true)->comment('图文消息使用');
            $table->string('pagepath', 150)->nullable(true)->comment('卡片消息使用');
            $table->unsignedTinyInteger('status')->default(0)->comment('是否启用');
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
        Schema::dropIfExists('message_template');
    }
}

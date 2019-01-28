<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatTaskMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_task_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('message_type');
            $table->text('message_content')->nullable();
            $table->text('image_message_content')->nullable();
            $table->string('tuwen_title')->nullable();
            $table->text('tuwen_desc')->nullable();
            $table->text('tuwen_image_url')->nullable();
            $table->text('tuwen_url')->nullable();
            $table->text('image_url')->nullable();
            $table->unsignedInteger('admin_user_id');
            $table->tinyInteger('status')->default(0);
            $table->dateTime('task_time');
            $table->string('media_id')->nullable();
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
        Schema::dropIfExists('wechat_task_messages');
    }
}

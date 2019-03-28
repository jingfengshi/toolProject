<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_registration', function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain',20)->nullable(false)->comment('域名');
            $table->string('full_domain',30)->nullable(false)->comment('可访问域名');
            $table->unsignedTinyInteger('status')->nullable(false)->comment('是否注册成功');
            $table->tinyInteger('number')->comment('期望注册数量');

            $table->string('taskno', '50')->nullable(false)->comment('注册任务no');
            $table->tinyInteger('task_status_code')->nullable(false)->comment('注册任务状态码');
            $table->string('errormsg', 100)->nullable(false)->comment('注册任务信息');

            $table->string('rr', '10')->nullable(false)->comment('主机记录');
            $table->string('ip',40)->nullable(true)->comment('解析的ip地址');
            $table->string('dns_taskid', '50')->nullable(false)->comment('批量解析taskid');
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
        Schema::dropIfExists('domain_registration');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitPageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visit_page', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gh_id', 40)->comment('小程序id');
            $table->string('ref_date',8)->comment('日期');
            $table->string('page_path',100)->nullable(true)->comment('页面路径');
            $table->integer('page_visit_pv')->default(0)->comment('访问次数');
            $table->integer('page_visit_uv')->default(0)->comment('访问人数');
            $table->float('page_staytime_pv')->default(0)->comment('次均停留时长');
            $table->integer('entrypage_pv')->default(0)->comment('进入页次数');
            $table->integer('exitpage_pv')->default(0)->comment('退出页次数');
            $table->integer('page_share_pv')->default(0)->comment('转发次数');
            $table->integer('page_share_uv')->default(0)->comment('转发人数');
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
        Schema::dropIfExists('visit_page');
    }
}

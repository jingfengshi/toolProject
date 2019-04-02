<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyVisitTrendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_visit_trend', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gh_id', 40)->comment('小程序id');
            $table->string('ref_date',8)->comment('日期');
            $table->integer('session_cnt')->comment('打开次数')->default(0);
            $table->integer('visit_pv')->comment('访问次数')->default(0);
            $table->integer('visit_uv')->comment('访问人数')->default(0);
            $table->integer('visit_uv_new')->comment('新用户数')->default(0);
            $table->float('stay_time_uv')->comment('人均停留时长')->default(0);
            $table->float('stay_time_session')->comment('次均停留时长')->default(0);
            $table->float('visit_depth')->comment('平均访问深度')->default(0);
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
        Schema::dropIfExists('daily_visit_trend');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertDefaultToDailySummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_summary', function (Blueprint $table) {
            $table->integer('visit_total')->default(0)->change();
            $table->integer('share_pv')->default(0)->change();
            $table->integer('share_uv')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_summary', function (Blueprint $table) {
            //
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWxShortDomainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wx_short_domain', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip');
            $table->text('origin_url');
            $table->text('short_url');
            $table->tinyInteger('generate_number');
            $table->unsignedInteger('admin_user_id');
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
        Schema::dropIfExists('wx_short_domain');
    }
}

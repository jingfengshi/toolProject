<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShortDomainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('short_domains', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip');
            $table->text('origin_url');
            $table->string('short_url');
            $table->date('validate_time');
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
        Schema::dropIfExists('short_domains');
    }
}

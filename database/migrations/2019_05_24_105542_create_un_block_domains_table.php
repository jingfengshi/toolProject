<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnBlockDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('un_block_domains', function (Blueprint $table) {
            $table->increments('id');
            $table->string('origin_url')->comment('原始链接');
            $table->unsignedInteger('user_id');
            $table->text('unblock_url');
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
        Schema::dropIfExists('un_block_domains');
    }
}

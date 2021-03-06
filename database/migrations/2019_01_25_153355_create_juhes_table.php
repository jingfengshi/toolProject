<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJuhesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('juhes', function (Blueprint $table) {
            $table->increments('id');
            $table->text('url');
            $table->unsignedInteger('admin_user_id');
            $table->string('type');
            $table->text('images');
            $table->string('site_url')->nullable();
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
        Schema::dropIfExists('juhes');
    }
}

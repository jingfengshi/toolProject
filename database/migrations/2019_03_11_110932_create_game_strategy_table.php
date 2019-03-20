<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameStrategyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_strategy', function (Blueprint $table) {
            $table->increments('id');
            $table->string('appletId',100)->nullable();
            $table->string('titleImg',100)->nullable();
            $table->string('titleName',50)->nullable(false);
            $table->text('content')->nullable(false);
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
        Schema::dropIfExists('game_strategy');
    }
}

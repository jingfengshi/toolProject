<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', [1, 2, 3, 4,]);
            $table->string('location_index', 10)->nullable(false);
            $table->string('ghId', 50)->nullable(false);
            $table->string('typeId', 50)->nullable();
            $table->string('jumpId', 20)->nullable(false);
            $table->string('jumpAppId',50)->nullable(false);
            $table->string('clickNub', 20)->nullable(false);
            $table->text('introduce')->nullable();
            $table->string('logo', 100);
            $table->string('jumpName', 30)->nullable(false);
            $table->string('aliasName', 30)->nullable(false);
            $table->string('jumpType', 10)->nullable(false);
            $table->text('extraData')->nullable();
            $table->string('jumpGhId', 50)->nullable(false);

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
        Schema::dropIfExists('games');
    }
}

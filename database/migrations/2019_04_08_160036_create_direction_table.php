<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDirectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('direction', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('origin_id')->unsigned()->index();
            $table->integer('destination_id')->unsigned()->index();
            $table->integer('distance')->unsigned();
            $table->integer('duration')->unsigned();
            $table->text('way');
            $table->foreign('origin_id')->references('id')->on('locality');
            $table->foreign('destination_id')->references('id')->on('locality');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('direction');
    }
}

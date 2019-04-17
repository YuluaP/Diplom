<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locality', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_ru', 255);
            $table->string('name_ua', 255);
            $table->string('name_eng', 255);
            $table->integer('region_id')->unsigned()->index();
            $table->foreign('region_id')->references('id')->on('region');
            $table->integer('area_id')->nullable()->unsigned()->index();
            $table->foreign('area_id')->references('id')->on('area');
            $table->decimal('lng', 11, 8)->index();
            $table->decimal('lat', 11, 8)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locality');
    }
}

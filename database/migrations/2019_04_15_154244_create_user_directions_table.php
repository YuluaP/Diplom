<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDirectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_directions', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->text('direction_name');
            $table->text('origin_address');
            $table->decimal('origin_lng', 11, 8)->index();
            $table->decimal('origin_lat', 11, 8)->index();
            $table->text('destination_address');
            $table->decimal('destination_lng', 11, 8)->index();
            $table->decimal('destination_lat', 11, 8)->index();
            $table->tinyInteger('private', 0);
            $table->text('comment')->nullable();
            $table->bigInteger('role_id')->unsigned();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_directions');
    }
}

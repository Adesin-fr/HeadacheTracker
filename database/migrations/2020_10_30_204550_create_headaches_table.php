<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeadachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('headaches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->date("date");
            $table->string("time");
            $table->tinyInteger("strength");
            $table->string("comments");
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
        Schema::dropIfExists('headaches');
    }
}

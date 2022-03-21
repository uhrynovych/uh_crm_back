<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options_value', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('options_id')->unsigned()->nullable();
            $table->foreign('options_id')->references('id')->on('options')->onDelete('cascade');
            $table->string('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options_value');
    }
}

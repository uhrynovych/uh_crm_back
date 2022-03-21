<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options_units', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->unsignedBigInteger('group_id')->unsigned()->nullable();
            $table->foreign('group_id')->references('id')->on('options_units_group')->onDelete('cascade');
            $table->enum('status', array('1', '0'))->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options_units');
    }
}

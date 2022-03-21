<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', array('input', 'checkbox', 'range', 'textarea', 'select'))->default('input');

            $table->unsignedBigInteger('default_unit_id')->unsigned()->nullable();
            $table->foreign('default_unit_id')->references('id')->on('options_units')->onDelete('set null');

            $table->unsignedBigInteger('group_id')->unsigned()->nullable();
            $table->foreign('group_id')->references('id')->on('options_units_group')->onDelete('set null');

            $table->enum('check_unit', array('1', '0'))->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options');
    }
}

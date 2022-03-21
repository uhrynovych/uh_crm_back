<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollaboratorsсontractorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collaborators_сontractor', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->date('birthday')->nullable();

            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('viber')->nullable();
            $table->string('telegram')->nullable();
            $table->string('skype')->nullable();
            $table->string('role');
            $table->string('description')->nullable();

            $table->unsignedBigInteger('photo_id')->unsigned()->nullable();
            $table->foreign('photo_id')->references('id')->on('media')->onDelete('set null');

            $table->unsignedBigInteger('contractor_id')->unsigned();
            $table->foreign('contractor_id')->references('id')->on('contractors')->onDelete('cascade');

            $table->enum('status', array('1', '0'))->default('1');

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
        Schema::dropIfExists('collaborators_сontractor');
    }
}

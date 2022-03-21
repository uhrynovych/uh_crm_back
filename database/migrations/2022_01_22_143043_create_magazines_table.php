<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagazinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magazines', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('logo_id')->unsigned()->nullable();
            $table->foreign('logo_id')->references('id')->on('media')->onDelete('set null');

            $table->string('url')->unique();
            $table->string('key')->unique();
            $table->text('description')->nullable()->change();
            $table->enum('status', array('1', '0'))->default('0');
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
        Schema::dropIfExists('magazines');
    }
}

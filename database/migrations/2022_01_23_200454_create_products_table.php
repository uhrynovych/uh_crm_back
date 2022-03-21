<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('ean');
            $table->integer('count');
            $table->integer('price');

            $table->unsignedBigInteger('cover_id')->unsigned()->nullable();
            $table->foreign('cover_id')->references('id')->on('media')->onDelete('set null');

            $table->unsignedBigInteger('product_group')->unsigned()->nullable();
            $table->foreign('product_group')->references('id')->on('products')->onDelete('set null');

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
        Schema::dropIfExists('products');
    }
}

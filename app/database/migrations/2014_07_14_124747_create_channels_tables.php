<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChannelsTables extends Migration {

    public function up(){

        if (!Schema::hasTable('channel_category')) {
            Schema::create('channel_category', function(Blueprint $table) {
                $table->increments('id');
                $table->string('title', 128)->nullable();
                $table->text('desc')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('channels')) {
            Schema::create('channels', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('category_id')->default(0)->unsigned()->nullable();
                $table->string('template',100)->nullable();
                $table->string('title', 128)->nullable();
                $table->string('link', 256)->nullable();
                $table->text('short')->nullable();
                $table->text('desc')->nullable();
                $table->string('file', 128)->nullable();
                $table->integer('image_id')->default(0)->unsigned()->nullable();
                $table->integer('gallery_id')->default(0)->unsigned()->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(){

        Schema::dropIfExists('channel_category');
        Schema::dropIfExists('channels');
    }
}

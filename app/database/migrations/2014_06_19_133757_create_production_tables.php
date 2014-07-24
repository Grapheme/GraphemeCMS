<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductionTables extends Migration {

	public function up(){

        if (!Schema::hasTable('products_category')) {
    		Schema::create('products_category', function(Blueprint $table) {
    			$table->increments('id');
                $table->string('title', 128)->nullable();
                $table->text('desc')->nullable();
    			$table->timestamps();
    		});
        }

        if (!Schema::hasTable('products')) {
    		Schema::create('products', function(Blueprint $table) {
    			$table->increments('id');
                $table->integer('category_id')->default(0)->unsigned()->nullable();
                $table->string('title', 128)->nullable();
                $table->text('short')->nullable();
                $table->text('desc')->nullable();
                $table->integer('image_id')->default(0)->unsigned()->nullable();
                $table->integer('gallery_id')->default(0)->unsigned()->nullable();
    			$table->timestamps();
    		});
        }
	}

	public function down(){

		Schema::dropIfExists('products_category');
		Schema::dropIfExists('products');
	}

}
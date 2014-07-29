<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductionTables extends Migration {

    private $table1 = 'products_category';
    private $table2 = 'products';
    private $table3 = 'products_meta';

	public function up(){

        if (!Schema::hasTable($this->table1)) {
            Schema::create($this->table1, function(Blueprint $table) {
                $table->increments('id');
                $table->string('title', 128)->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
            echo(' + ' . $this->table1 . PHP_EOL);
        } else {
            echo('...' . $this->table1 . PHP_EOL);
        }

        if (!Schema::hasTable($this->table2)) {
            Schema::create($this->table2, function(Blueprint $table) {
                $table->increments('id');
                $table->integer('category_id')->default(0)->unsigned()->nullable();
                $table->boolean('publication')->default(1)->unsigned()->nullable();
                $table->integer('image_id')->default(0)->unsigned()->nullable();
                $table->integer('gallery_id')->default(0)->unsigned()->nullable();
                $table->timestamps();
                $table->index('publication');
            });
            echo(' + ' . $this->table2 . PHP_EOL);
        } else {
            echo('...' . $this->table2 . PHP_EOL);
        }

        if (!Schema::hasTable($this->table3)) {
            Schema::create($this->table3, function(Blueprint $table) {
                $table->increments('id');
                $table->integer('product_id')->default(0)->unsigned()->nullable();
                $table->string('language',10)->nullable();
                $table->string('title',100)->nullable();
                $table->float('price')->default(0)->nullable();
                $table->string('short_title', 20)->nullable();
                $table->mediumText('preview')->nullable();
                $table->Text('content')->nullable();
                $table->string('seo_url',255)->nullable();
                $table->string('seo_title',255)->nullable();
                $table->text('seo_description')->nullable();
                $table->text('seo_keywords')->nullable();
                $table->string('seo_h1')->nullable();
                $table->timestamps();
                $table->index('product_id');
                $table->index('language');
                $table->index('seo_url');
            });
            echo(' + ' . $this->table3 . PHP_EOL);
        } else {
            echo('...' . $this->table3 . PHP_EOL);
        }
	}

	public function down(){

		Schema::dropIfExists($this->table1);
		Schema::dropIfExists($this->table2);
		Schema::dropIfExists($this->table3);
	}

}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReviewsTable extends Migration {

    public $table = 'reviews';

	public function up(){
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {
                $table->increments('id');
                $table->string('language',10)->nullable();
                $table->string('name',100)->nullable();
                $table->string('position',100)->nullable();
                $table->mediumText('content')->nullable();
                $table->string('slug',64)->nullable();
                $table->string('template',100)->nullable();
                $table->boolean('publication')->default(1)->unsigned()->nullable();
                $table->integer('image_id')->default(0)->unsigned()->nullable();
                $table->integer('gallery_id')->default(0)->unsigned()->nullable();
                $table->timestamps();
                $table->date('published_at');
                $table->index('publication');
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }
	}


	public function down(){
        Schema::dropIfExists($this->table);
        echo(' - ' . $this->table . PHP_EOL);
	}

}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventsTable extends Migration {

    private $table1 = 'events';
    private $table2 = 'events_meta';

    public function up(){
        if (!Schema::hasTable($this->table1)) {
            Schema::create($this->table1, function(Blueprint $table) {
                $table->increments('id');
                $table->string('template',100)->nullable();
                $table->string('slug',100)->nullable();
                $table->boolean('publication')->default(1)->unsigned()->nullable();
                $table->integer('image_id')->default(0)->unsigned()->nullable();
                $table->integer('gallery_id')->default(0)->unsigned()->nullable();
                $table->date('published_at');
                $table->timestamps();
                $table->index('publication');
                $table->index('published_at');
            });
            echo(' + ' . $this->table1 . PHP_EOL);
        } else {
            echo('...' . $this->table1 . PHP_EOL);
        }

        if (!Schema::hasTable($this->table2)) {
            Schema::create($this->table2, function(Blueprint $table) {
                $table->increments('id');
                $table->integer('event_id')->default(0)->unsigned()->nullable();
                $table->string('language',10)->nullable();
                $table->string('title',100)->nullable();
                $table->mediumText('preview')->nullable();
                $table->Text('content')->nullable();
                $table->string('seo_url',255)->nullable();
                $table->string('seo_title',255)->nullable();
                $table->text('seo_description')->nullable();
                $table->text('seo_keywords')->nullable();
                $table->string('seo_h1')->nullable();
                $table->timestamps();
                $table->index('event_id');
                $table->index('language');
                $table->index('seo_url');
            });
            echo(' + ' . $this->table2 . PHP_EOL);
        } else {
            echo('...' . $this->table2 . PHP_EOL);
        }
    }


    public function down(){
        Schema::dropIfExists($this->table1);
        echo(' - ' . $this->table1 . PHP_EOL);

        Schema::dropIfExists($this->table2);
        echo(' - ' . $this->table2 . PHP_EOL);
    }
}

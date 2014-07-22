<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActionsTable extends Migration {

    public $table = 'actions';

	public function up(){
        if (!Schema::hasTable($this->table)) {
    		Schema::create($this->table, function(Blueprint $table) {			
    			$table->increments('id');
    			$table->integer('group_id');
    			$table->string('module', 32)->nullable();
    			$table->string('action', 32)->nullable();
    			$table->integer('status')->default(0)->unsigned();
    		});
            echo(' + ' . $this->table . PHP_EOL);
        } else
            echo('...' . $this->table . PHP_EOL);
	}

	public function down(){
		Schema::dropIfExists($this->table);
        echo(' - ' . $this->table . PHP_EOL);
	}

}

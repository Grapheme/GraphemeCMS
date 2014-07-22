<?php

class ProductCategory extends BaseModel {

	protected $guarded = array();
	protected $table = 'products_category';
    #public $timestamps = false;

	public static $rules = array(
		'title' => 'required',
		#'desc' => 'required',
	);

	public function count_products(){
		return Product::where('category_id', $this->id)->count();
	}

	public function products(){
		return Product::where('category_id', $this->id)->orderBy("title", "ASC")->get();
	}

    /*
	public function group(){
		return $this->hasOne('Group', 'group_id', 'id');
	}

	public function module(){
		return $this->hasOne('Modules', 'module', 'name');
	}
    */

}
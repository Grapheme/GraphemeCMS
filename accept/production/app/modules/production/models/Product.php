<?php

class Product extends BaseModel {

	protected $guarded = array();
	protected $table = 'products';
    #public $timestamps = false;

	public static $rules = array(
		'category_id' => 'required|min:1',
	);

	public function photo() {

        return Photo::where('id', $this->image_id)->first();
	}

    public  function images(){
        return $this->belongsTo('Photo','image_id');
    }

    public  function gallery(){
        return $this->hasMany('Rel_mod_gallery','unit_id');
    }

    public  function meta(){
        return $this->hasMany('ProductsMeta','product_id');
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
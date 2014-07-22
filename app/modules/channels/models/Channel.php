<?php

class Channel extends BaseModel {

	protected $guarded = array();
	protected $table = 'channels';
    #public $timestamps = false;

	public static $rules = array(
		'title' => 'required',
		'category_id' => 'required|min:1',
	);

	public function photo() {

        return Photo::where('id', $this->image_id)->first();
	}
    public  function images(){
        return $this->belongsTo('Photo','image_id');
    }
}
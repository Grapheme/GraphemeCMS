<?php

class ChannelCategory extends BaseModel {

	protected $guarded = array();
	protected $table = 'channel_category';
    #public $timestamps = false;

	public static $rules = array(
		'slug' => 'required',
		'title' => 'required',
		#'desc' => 'required',
	);

	public function count_channels(){
		return Channel::where('category_id', $this->id)->count();
	}

	public function channels(){
		return Channel::where('category_id', $this->id)->orderBy("title", "ASC")->get();
	}

    public function channel(){
        return $this->hasMany('Channel','category_id');
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
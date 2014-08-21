<?php

class Tag extends BaseModel {

	protected $guarded = array();

	protected $table = 'tags';

	public static $order_by = "created_at DESC";

	public static $rules = array(
		'module' => 'required',
		'unit_id' => 'required|integer',
		'tag' => 'required|min:3|max:64',
	);

}
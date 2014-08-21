<?php

class Events extends BaseModel {

    protected $guarded = array();

    protected $table = 'events';

    public static $order_by = "events.published_at DESC,events.id DESC";

    public static $rules = array(
        'slug' => 'required',
        'title' => 'required'
    );

    public function photo() {

        return $this->hasOne('Photo','id','image_id');
    }

    public  function images(){
        return $this->belongsTo('Photo','image_id');
    }

    public  function meta(){
        return $this->hasMany('EventsMeta','event_id');
    }
}
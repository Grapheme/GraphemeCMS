<?php

return array (
	'host'    => '127.0.0.1',
	'port'    => 3312,
	'indexes' => array (
		'channelsIndex' => array('table'=>'channels','column'=>'id'),
		'productsIndex' => array('table'=>'products','column'=>'id'),
		'reviewsIndex' => array('table'=>'reviews','column'=>'id'),
		'pagesIndex' => array('table'=>'i18n_pages','column'=>'id')
	)
);

<?php

class Helper {

	/*
	| Функция возвращает 2х-мерный массив который формируется из строки.
	| Строка сперва разбивается по запятой, потом по пробелам.
	| Используется пока для разбора строки сортировки model::orderBy() в ShortCodes
	*/
    ## from BaseController
	public static function stringToArray($string){

		$ordering = array();
		if(!empty($string)):
			if($order_by = explode(',',$string)):
				foreach($order_by as $index => $order):
					if($single_orders = explode(' ',$order)):
						foreach($single_orders as $single_order):
							$ordering[$index][] = strtolower($single_order);
						endforeach;
					endif;
				endforeach;
			endif;
		endif;
		return $ordering;
	}
    
    public static function d($array) {
        echo "<pre style='text-align:left'>" . print_r($array, 1) . "</pre>";
    }

    public static function dd($array) {
        self::d($array);
        die;
    }

    public static function dd_($array) {
        return false;
    }

    public static function layout($file = '') {
        $layout = Config::get('app.template');
        if (!$layout)
            $layout = 'default';
        return "templates." . $layout . ($file ? '.'.$file : '');
    }

    public static function acclayout($file = '') {
        $layout = AuthAccount::getStartPage();
        if (!$layout)
            $layout = 'default';
        return "templates." . $layout . ($file ? '.'.$file : '');
    }

    public static function inclayout($file) {
        if (!$file)
            return false;

        $layout = Config::get('app.template');

        if (!$layout)
            $layout = 'default';

        $full = base_path() . "/app/views/templates/" . $layout . '/' . $file;

        if(!file_exists($full))
            $full .= ".blade.php";

        #if (!file_exists($full))
        #    return false;

        return $full;
    }

    public static function rdate($param = "j M Y", $time=0, $lower = true) {
        if (!is_int($time) && !is_numeric($time))
            $time = strtotime($time);
    	if (intval($time)==0)
            $time=time();
    	$MonthNames=array("Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря");
    	if(strpos($param,'M')===false)
            return date($param, $time);
    	else {
            $month = $MonthNames[date('n', $time)-1];
            if ($lower)
                $month = mb_strtolower($month);
            return date(str_replace('M', $month, $param), $time);
        }
    }

    public static function preview($text, $count = 10, $threedots = true) {

        $words = array();
        $temp = explode(" ", strip_tags($text));

        foreach ($temp as $t => $tmp) {
            $tmp = trim($tmp, ".,?!-+/");
            if (!$tmp)
                continue;
            $words[] = $tmp;
            if (count($words) >= $count)
                break;
        }

        $preview = trim(implode(" ", $words));

        if (mb_strlen($preview) < mb_strlen(trim(strip_tags($text))) && $threedots)
            $preview .= "...";

        return $preview;
    }

    public static function arrayForSelect($object, $key = 'id', $val = 'name') {
        if (!isset($object) || !is_object($object))
            return false;

        $array = array();
        foreach ($object as $o => $obj) {
            $array[@$obj->$key] = @$obj->$val; 
        }

        return $array;
    }
}


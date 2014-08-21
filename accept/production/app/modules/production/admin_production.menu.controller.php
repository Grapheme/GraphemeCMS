<?php

class AdminProductionMenuController extends BaseController {

    public static $name = 'production';
    public static $group = 'production';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {
        #
    }

    ## Actions of module (for distribution rights of users)
    ## return false;   # for loading default actions from config
    ## return array(); # no rules will be loaded
    public static function returnActions() {
        return array(
        	'view'   => 'Отображать в меню',

        	'category_view'   => 'Категории - Просмотр',
        	'category_create' => 'Категории - Создание',
        	'category_edit'   => 'Категории - Редактирование',
        	'category_delete' => 'Категории - Удаление',
        
        	'product_view'   => 'Продукты - Просмотр',
        	'product_create' => 'Продукты - Создание',
        	'product_edit'   => 'Продукты - Редактирование',
        	'product_delete' => 'Продукты - Удаление',
        );
    }

    ## Info about module (now only for admin dashboard & menu)
    public static function returnInfo() {
        return array(
        	'name' => self::$name,
        	'group' => self::$group,
        	'title' => 'Продукция', 
            'visible' => 1,
        );
    }


    ## Menu elements of the module
    public static function returnMenu() {
        return array(
            array(
            	'title' => 'Продукция',
            	'link' => '#',
                'class' => 'fa-flask', 
                'permit' => 'view',
                'menu_child' => array(
                    array(
                    	'title' => 'Категории',
                        'link' => self::$group . "/categories",
                        'class' => 'fa-database', 
                        'permit' => 'view',
                    ),
                    array(
                    	'title' => 'Продукты',
                        'link' => self::$group . "/products",
                        'class' => 'fa-puzzle-piece', 
                        'permit' => 'view',
                    ),
                ),
            ),
        );
        /*
        return array(
            array(
            	'title' => 'Категории',
                'link' => self::$group . "/categories",
                'class' => 'fa-crosshairs', 
                'permit' => 'view',
            ),
            array(
            	'title' => 'Продукты',
                'link' => self::$group . "/products",
                'class' => 'fa-ticket', 
                'permit' => 'view',
            ),
        );
        #*/
    }

    /****************************************************************************/

	public function __construct(){
		#
	}


}

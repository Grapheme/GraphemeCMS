<?php

class AdminChannelMenuController extends BaseController {

    public static $name = 'channels_menu';
    public static $group = 'channels';

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
        
        	'channel_view'   => 'Канал - Просмотр',
        	'channel_create' => 'Канал - Создание',
        	'channel_edit'   => 'Канал - Редактирование',
        	'channel_delete' => 'Канал - Удаление',
        );
    }

    ## Info about module (now only for admin dashboard & menu)
    public static function returnInfo() {
        return array(
        	'name' => self::$name,
        	'group' => self::$group,
        	'title' => 'Информационные блоки',
            'visible' => 1,
        );
    }


    ## Menu elements of the module
    public static function returnMenu() {
        return array(
            array(
            	'title' => 'Инфоблоки',
            	'link' => '#',
                'class' => 'fa-info',
                'permit' => 'view',
                'menu_child' => array(
                    array(
                    	'title' => 'Категории',
                        'link' => self::$group . "/categories",
                        'class' => 'fa-list-ul',
                        'permit' => 'view',
                    ),
                    array(
                    	'title' => 'Каналы',
                        'link' => self::$group . "/channels",
                        'class' => 'fa-th-list',
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

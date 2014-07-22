<?php

class SystemModules {

	public static function getSidebarModules(){

		$start_page = AuthAccount::getStartPage();

        $menu = array();
        
        ## Modules info
        $mod_info = Config::get('mod_info');
        $mod_menu = Config::get('mod_menu');
        #Helper::dd($mod_info);
        #Helper::dd($mod_menu);

        ## If exists menu elements...
        if (isset($mod_menu) && is_array($mod_menu) && count($mod_menu)) {
            foreach( $mod_menu as $mod_name => $menu_elements ) {

                if( is_array($menu_elements) && count($menu_elements) ) {
                    foreach( $menu_elements as $m => $menu_element ) {

                        #Helper::d($menu_element); continue;
                        #Helper::d($menu_element); continue;

                        ## If permit to view menu element
                        $permit = isset($menu_element['permit']) ? Allow::action($mod_name, $menu_element['permit']) : false;

                        #Helper::d( $menu_element['title'] . " - " . (int)$permit );

                        if (
                            Allow::module($mod_name) && $permit
                        )
                            $menu[] = $menu_element;
                    }
                }
            }
        }
        #Helper::dd($menu);
        #die();

        ## System permissions
        if (Allow::action('system', 'permissions')) {

            $menu_child = array();
            if (Allow::action('users', 'view')) {
                $menu_child[] = array(
                	'title' => 'Пользователи', #trans('menu.pages.menu_title'), 
                    'link' => 'users',
                    'class' => 'fa-user', 
                );
            }
            if (Allow::action('groups', 'view')) {
                $menu_child[] = array(
                	'title' => 'Группы', #trans('menu.pages.menu_title'), 
                    'link' => 'groups',
                    'class' => 'fa-group', 
                );
            }

            $menu[] = array(
                'title' => 'Права доступа', #trans('admin.users'),
                'link' => '#',
                'class' => 'fa-lock',
                'system' => 1,
                'menu_child' => $menu_child,
            );
        }
        
        ## System settings
        if (Allow::action('system', 'settings_view')) {
            $menu[] = array(
                'title' => trans('admin.settings'),
                'link' => 'settings',
                'class' => 'fa-cog',
                'system' => 1,
            );
        }
        
        #Helper::d($menu);

        return $menu;
	}

	/*
	| Функция возвращает всю запись о модуле.
	| Если Модуль не существует - возвращается TRUE, это нужно для возможности дальнейшей проверки на уровне ролей групп пользователей
	| Allow::valid_access()
	*/

	public static function getModules($name = NULL, $index = NULL){

        ## mod_info - информация о модулях, загружается в routes.php
        $modules = array();
        $mod_info = Config::get('mod_info');
        #Helper::dd($mod_info);

        foreach( $mod_info as $mod_name => $info ) {
            if (
                @$info['visible']
                #|| @$info['show_in_menu']
            )
                $modules[$mod_name] = $info;
        }
        #Helper::dd($modules);

		if(is_null($name)):
			return $modules;
		else:
			if(isset($modules[$name])):
				if(is_null($index)):
					return $modules[$name];
				elseif(isset($modules[$name][$index])):
					return $modules[$name][$index];
				endif;
			else:
				return TRUE;
			endif;
		endif;
	}
}

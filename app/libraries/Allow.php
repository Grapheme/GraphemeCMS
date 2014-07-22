<?php

class Allow {

	/**
	 * Проверяет, есть ли у текущего авторизованного пользователя разрешение на выполнение определенного действия в модуле
     * Рекомендуется использовать в шаблонах, например при выводе ссылок на страницы редактирования/удаления.
     *
     * @param string $module_name
     * @param string $action
     * @return bool
	 */
	public static function action($module_name, $action, $check_module_enabled = false, $admin_grants_all = true){

		$access = false;
        ## Check auth
		if(Auth::check()) {
            ## Get current user group
            $user_group = Auth::user()->group;

            #Helper::dd($user_group);

            /**
             * @todo Полные права на действия админа, т.к. новые имена модулей не совпадают со старыми ролями ( news != admin_news ). Нужно во всех модулях поменять valid_action_permission на Action
             */
            ## Grants all to ADMIN
            if ($user_group->id == '1' && $admin_grants_all)
                $access = true;
            else {
                if ($check_module_enabled) {
                    ## Find module
    			    $module = Module::where('name', $module_name)->first();
                } else
                    $module = true;
                ## Check all conditions
    			if(!is_null($user_group) && !is_null($module) && !is_null($action)) {
                    ## If user group is not ADMIN
                    $permission = Action::where('group_id', $user_group->id)->where('module', $module_name)->where('action', $action)->first();
                    ## If permission exists & is activated
                    if (!is_null($permission) && $permission->status == '1') {
                        $access = true;
                    }
    			} else {
                    #Helper::d($user_group . " / " . $module_name . " = " . (int)$module . " / " . $action);
                }
            }
		}
        #Helper::d($module_name . " @ " . $action . " = " . $access);
		return $access;
	}

	public static function menu($module_name){
        return self::action($module_name, 'view', false, false);
    }

	/**
	 * Прерывает выполнение дальнейших действий, если у пользователя нет прав на выполнение конкретного действия в модуле.
     * Рекомендуется использовать в самом начале защищенного метода класса, например при редактировании или удалении данных.
     *
     * @param string $module_name
     * @param string $action
     * @return ABORT
	 */
	public static function permission($module_name, $action){
        if (!self::action($module_name, $action, false))
            App::abort(403);
    }

	/**
	 * Проверяет, доступен ли модуль (включен ли он).
     * Рекомендуется использовать в шаблонах, при подключении элементов расширенной формы ExtForm::<element>
     *
     * @param string $module_name
     * @return bool
	 */
	public static function module($module_name){
        return (bool)(Module::where('name', $module_name)->exists() && Module::where('name', $module_name)->first()->on == 1);
	}


    /**
     * Alias for Allow::module(<module_name>);
     */
	public static function enabled_module($module_name){
        return self::module($module_name);
	}
    /**
     * Alias for Allow::module(<module_name>);
     */
	public static function valid_access($module_name){
        return self::module($module_name);
	}

}

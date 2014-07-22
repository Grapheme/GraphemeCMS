<?php

class AdminSettingsController extends BaseController {

    public static $name = 'settings';
    public static $group = 'system';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {

        $class = __CLASS__;
        $name = self::$name;
        Route::group(array('before'=>'auth', 'prefix'=>'admin'), function() use ($class, $name) {
        	Route::controller($name, $class);
        });
    }

    ## Actions of module (for distribution rights of users)
    public static function returnActions() {
        return array(
        	'view'   => 'Просмотр',
        	'edit'   => 'Редактирование',
        );
    }

    ## Info about module (now only for admin dashboard & menu)
    public static function returnInfo() {
        return array(
        	'name' => self::$name,
        	'group' => self::$group,
        	'title' => 'Настройки', 
            'visible' => 0,
        );
    }

    /****************************************************************************/

	public function __construct(){
		

        $this->module = array(
            'name' => self::$name,
            'group' => self::$group,
            'tpl' => static::returnTpl('admin/settings'),
            'gtpl' => static::returnTpl(),
        );
        View::share('module', $this->module);
	}
	
	public function getIndex(){
		
		$settings = Setting::retArray();
		#$languages = Language::retArray();
		return View::make($this->module['tpl'].'index', compact('settings','languages'));
	}

	public function postAdminlanguagechange(){
		
		$id = Input::get('id');
		$model = setting::where('name', 'admin_language')->first();
		$model->value = language::find($id)->code;
		$model->save();
	}

	public function postModule(){
		
		$json_request = array('status'=>TRUE, 'responseText'=>'Сохранено');

		if($module = Module::where('name', Input::get('name'))->first()) {
			$module->update(array('on'=>Input::get('value')));
			$module->touch();
		} else {
			Module::create(array('name'=>Input::get('name'), 'on'=>Input::get('value')));
		}

		if(Input::get('value') == 1) {
			$json_request['responseText'] = "Модуль &laquo;".SystemModules::getModules(Input::get('name'), 'title')."&raquo; включен";
		} else {
			$json_request['responseText'] = "Модуль &laquo;".SystemModules::getModules(Input::get('name'), 'title')."&raquo; выключен";
		}

        #$json_request['responseText'] = print_r(SystemModules::getModules(Input::get('name')), 1);

		return Response::json($json_request, 200);
	}
}

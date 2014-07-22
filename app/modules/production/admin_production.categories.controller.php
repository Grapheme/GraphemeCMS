<?php

class AdminProductionCategoriesController extends BaseController {

    public static $name = 'categories';
    public static $group = 'production';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {

        $class = __CLASS__;
        Route::group(array('before' => 'auth', 'prefix' => $prefix), function() use ($class) {
        	Route::controller($class::$group."/".$class::$name, $class);
        });
    }

    ## Actions of module (for distribution rights of users)
    ## return false;   # for loading default actions from config
    ## return array(); # no rules will be loaded
    public static function returnActions() {
        #
    }

    ## Info about module (now only for admin dashboard & menu)
    public static function returnInfo() {
        #
    }
    
    /****************************************************************************/

	public function __construct(){

		#$this->beforeFilter('groups');

        $this->module = array(
            'name' => self::$name,
            'group' => self::$group,
            'rest' => self::$group . "/" . self::$name,
            'tpl'  => static::returnTpl('admin/' . self::$name),
            'gtpl' => static::returnTpl(),
        );
        View::share('module', $this->module);
	}

	public function getIndex(){

        Allow::permission($this->module['group'], 'category_view');

		$categories = ProductCategory::all();
		return View::make($this->module['tpl'].'index', compact('categories'));
	}

    /****************************************************************************/

	public function getCreate(){

        Allow::permission($this->module['group'], 'category_create');

		return View::make($this->module['tpl'].'create', array());
	}

	public function postStore(){

        Allow::permission($this->module['group'], 'category_create');

		$json_request = array('status'=>FALSE, 'responseText'=>'', 'responseErrorText'=>'', 'redirect'=>FALSE);
		
		$input = array(
            'title' => Input::get('title'),
            'desc' => Input::get('desc'),
        );

		$validation = Validator::make($input, ProductCategory::$rules);
		if($validation->passes()) {

			ProductCategory::create($input);
			#return link::auth('groups');

			$json_request['responseText'] = "Категория создана";
			#$json_request['responseText'] = print_r(Input::get('actions'), 1);
			$json_request['redirect'] = link::auth($this->module['rest']);
			$json_request['status'] = TRUE;

		} else {
			#return Response::json($v->messages()->toJson(), 400);
			$json_request['responseText'] = 'Неверно заполнены поля';
			$json_request['responseErrorText'] = implode($validation->messages()->all(),'<br />');
		}
		return Response::json($json_request, 200);
	}

    /****************************************************************************/

	public function getEdit($id){

        Allow::permission($this->module['group'], 'category_edit');

		$category = ProductCategory::find($id);
		return View::make($this->module['tpl'].'edit', compact('category'));
	}

	public function postUpdate($id){

        Allow::permission($this->module['group'], 'category_edit');

		$json_request = array('status'=>FALSE, 'responseText'=>'', 'responseErrorText'=>'', 'redirect'=>FALSE);
		if(!Request::ajax())
            return App::abort(404);

		if(!$category = ProductCategory::find($id)) {
			$json_request['responseText'] = 'Запрашиваемая категория не найдена!';
			return Response::json($json_request, 400);
		}
        
        $input = Input::all();
        
		$validation = Validator::make($input, ProductCategory::$rules);
		if($validation->passes()):

            ## Update group
			$category->update($input);

			$json_request['responseText'] = 'Категория обновлена';
			#$json_request['responseText'] = print_r($group_id, 1);
			#$json_request['responseText'] = print_r($group, 1);
			#$json_request['responseText'] = print_r(Input::get('actions'), 1);
			#$json_request['responseText'] = print_r($group->actions(), 1);
			#$json_request['redirect'] = link::auth('groups');
			$json_request['status'] = TRUE;
		else:
			$json_request['responseText'] = 'Неверно заполнены поля';
			$json_request['responseErrorText'] = implode($validation->messages()->all(), '<br />');
		endif;
        
		return Response::json($json_request, 200);
	}

    /****************************************************************************/

	public function deleteDestroy($id){

        Allow::permission($this->module['group'], 'category_delete');

		if(!Request::ajax())
            return App::abort(404);

		$json_request = array('status'=>FALSE, 'responseText'=>'');

	    $deleted = Product::where('category_id', $id)->delete();
	    $deleted2 = ProductCategory::find($id)->delete();
		$json_request['responseText'] = 'Категория удалена';
		$json_request['status'] = TRUE;
		return Response::json($json_request, 200);
	}

}

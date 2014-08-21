<?php

class AdminTagsController extends BaseController {

    public static $name = 'tags';
    public static $group = 'tags';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {
        $class = __CLASS__;
        Route::group(array('before' => 'auth', 'prefix' => $prefix), function() use ($class) {
        	Route::controller($class::$group, $class);
        });
    	Route::get("ajax/".$class::$group."/search-suggest", $class."@SearchSuggest");
    }

    ## Shortcodes of module
    public static function returnShortCodes() {
        ##
    }

    ## Extended Form elements of module
    public static function returnExtFormElements() {

        $mod_tpl = static::returnTpl();

    	ExtForm::add(
            ## Name of element
            "tags",
            ## Closure for templates (html-code)
            function($name = 'tags', $value = '', $params = null) use ($mod_tpl) {
                ## default template
                $tpl = "extform_tags";
                ## custom template
                if (@$params['tpl']) {
                    $tpl = $params['tpl'];
                    unset($params['tpl']);
                }
                ## if gettin' value - is Eloquent object (set of Tag-model elements)
                ## make array
                if (is_object($value)) {
                    $temp = array();
                    foreach ($value as $t => $tag)
                        $temp[] = $tag->tag;
                    $value = $temp;
                    unset($temp);
                }
                ## if gettin' value - array, then make string
                if (is_array($value))
                    $value = implode(",", $value);
                ## return view with form element
                return View::make($mod_tpl.$tpl, compact('name', 'value', 'params'));                
    	    },
            ## Processing results closure
            #function($module, $unit_id, $tags = '') {
            function($params) {

                #dd($params);

                $module   = isset($params['module']) ? $params['module'] : false;
                $unit_id  = isset($params['unit_id']) ? $params['unit_id'] : false;
                $tags     = isset($params['tags']) ? $params['tags'] : false;
                $module = (string)trim($module);
                $unit_id = (int)trim($unit_id);
                if (!$module || !$unit_id)
                    return false;
                if (is_string($tags)) {
                    $tags = trim($tags);
                    if (!$tags) {
                        $tags = array();
                    } else {
                        if (strpos($tags, ',')) {
                            $temp = explode(',', $tags);
                            $tags = array();
                            foreach ($temp as $tmp) {
                                $tmp = trim($tmp);
                                if ($tmp)
                                    $tags[] = $tmp;
                            }
                            unset($temp);
                        } else {
                            $tags = array($tags);
                        }
                    }
                }

                /*
                    DELETE ALL TAGS
                */
                $exists = Tag::where('module', $module)->where('unit_id', $unit_id)->get();
                #dd($exists);
                $debug = array();
                ## Из уже существующих тегов оставляем только те, которые есть в переданном массиве тегов
                foreach ($exists as $e => $exist) {
                    $debug[] = 'TEST: ' . (string)$exist->tag . " / " . print_r($tags, 1);
                    if (is_int($key = array_search((string)$exist->tag, $tags))) {
                        $debug[] = 'exist: ' . $exist->tag;
                        unset($tags[$key]);
                    } else {
                        $debug[] = 'delete: ' . $exist->tag;
                        $exist->delete();
                    }
                }
                ## Если в переданном массиве есть недобавленные ранее теги - добавляем их
                if (is_array($tags) && count($tags)) {
                    foreach ($tags as $t) {
                        $debug[] = 'new: ' . $t;
                        $tag = new Tag;
                        $tag->module = $module;
                        $tag->unit_id = $unit_id;
                        $tag->tag = $t;
                        $tag->save();
                    }
                }
                #dd($debug);
    	    }
        );
    }
    
    ## Actions of module (for distribution rights of users)
    ## return false;   # for loading default actions from config
    ## return array(); # no rules will be loaded
    public static function returnActions() {
        return array(
        	'view'   => 'Просмотр',
        	'create' => 'Создание',
        	'edit'   => 'Редактирование',
        	'delete' => 'Удаление',
        );
    }

    ## Info about module (now only for admin dashboard & menu)
    public static function returnInfo() {
        return array(
        	'name' => self::$name,
        	'group' => self::$group,
        	'title' => 'Теги',
            'visible' => 1,
        );
    }

    ## Menu elements of the module
    public static function returnMenu() {
        return array(
            array(
            	'title' => 'Теги',
                'link' => self::$group,
                'class' => 'fa-tags', 
                'permit' => 'view',
            ),
        );
    }

    /****************************************************************************/

	public function __construct(){

		$this->beforeFilter('tags');

        $this->module = array(
            'name' => self::$name,
            'group' => self::$group,
            'rest' => self::$group,
            'tpl' => static::returnTpl('admin'),
            'gtpl' => static::returnTpl(),
        );
        View::share('module', $this->module);
	}

    ## All unique tags
	public function getIndex(){

		#$tags = Tag::orderBy('created_at', 'DESC')->get();
		#$tags = Tag::select('tag')->distinct()->orderBy('tag', 'ASC')->take(5)->offset(5)->get();#->paginate(10);
        
        $page = Input::get('page')>0 ? Input::get('page')-1 : 0;
        $limit = 50;
        
		$tags_all = Tag::select('tag')->distinct()->get();#->paginate(10);
		$tags  = Tag::select('tag')->distinct()->orderBy('tag', 'ASC')->take($limit)->offset($limit*$page)->get();#->paginate(10);
        $count = count($tags_all);
        
        $paginator = Paginator::make((array)$tags_all, $count, $limit);
        
		return View::make($this->module['tpl'].'index', compact('tags', 'paginator'));
	}

    ## All records with current tag
	public function getView($tag){

		$tags = Tag::where('tag', $tag)->orderBy('created_at', 'DESC')->get();
		return View::make($this->module['tpl'].'view', compact('tag', 'tags'));
	}

    /************************************************************************************/

	public function getCreate(){

		return View::make($this->module['tpl'].'create', array());
	}

	public function postStore(){

		if(!Request::ajax())
            return App::abort(404);
            
		$input = Input::all();
		$json_request = array('status'=>FALSE, 'responseText'=>'', 'responseErrorText'=>'', 'redirect'=>FALSE);
		$validator = Validator::make($input, Tag::$rules);
		if($validator->passes()) {

		    #$json_request['responseText'] = "<pre>" . print_r($_POST, 1) . "</pre>";
		    #return Response::json($json_request,200);

			#self::saveNewsModel();
            $id = Tag::create($input)->id;
			#$json_request['responseText'] = 'Место создано';
			$json_request['redirect'] = link::auth( $this->module['rest'] );
			$json_request['status'] = TRUE;
		} else {
			$json_request['responseText'] = 'Неверно заполнены поля';
			$json_request['responseErrorText'] = $validator->messages()->all();
		}
		return Response::json($json_request,200);
	}

    /************************************************************************************/

	public function getEdit($id){

		$tag = Tag::findOrFail($id);
		return View::make($this->module['tpl'].'edit', compact('tag'));
	}

	public function postUpdate($id){

		if(!Request::ajax())
            return App::abort(404);

		$input = Input::all();
		$json_request = array('status'=>FALSE, 'responseText'=>'', 'responseErrorText'=>'', 'redirect'=>FALSE);
		$validator = Validator::make($input, Tag::$rules);
		if($validator->passes()) {
            $tag = Tag::find($id)->update($input);
            #$place->update($input);
			$json_request['responseText'] = 'Тег обновлен';
			#$json_request['redirect'] = link::auth( $this->module['rest'] );
			$json_request['status'] = TRUE;
		} else {
			$json_request['responseText'] = 'Неверно заполнены поля';
			$json_request['responseErrorText'] = $validator->messages()->all();
		}
		return Response::json($json_request, 200);
	}

    /************************************************************************************/

	public function deleteDestroy($id){

		if(!Request::ajax())
            return App::abort(404);

		$json_request = array('status'=>FALSE, 'responseText'=>'');
	    $deleted = Tag::find($id)->delete();
		$json_request['responseText'] = 'Тег удален';
		$json_request['status'] = TRUE;
		return Response::json($json_request, 200);
	}
    
	public function deleteDestroyall($tag){

		if(!Request::ajax())
            return App::abort(404);

		$json_request = array('status'=>FALSE, 'responseText'=>'');
	    $deleted = Tag::where('tag', $tag)->delete();
		$json_request['responseText'] = 'Тег удален';
		$json_request['status'] = TRUE;
		return Response::json($json_request, 200);
	}

    /************************************************************************************/

    ## Search Suggest - POST-request with string line and/or module name
	public function SearchSuggest(){

        $tags = new Tag;

        $str = Input::get("str");
        if ($str) {
            $str = iconv("", "UTF-8", $str);
            $tags = $tags->where('tag', 'like', $str.'%');
        }

        $mod = Input::get("mod");
        if ($mod) {
            $tags = $tags->where('module', '=', $mod);
        }
        
        $tags = $tags
            ->select('tag')->distinct()
            ->take(10)->get();

        #$suggests = array("111", "222", "333");
        $suggests = array();
        foreach($tags as $tag)
            $suggests[] = $tag->tag;

        #Helper::dd($suggests);

		return Response::json($suggests, 200);
	}


    /**
     * Insert form element in external module for realize tags functionality
     * @example:
            @if (Allow::enabled_module('admin_tags') && method_exists('AdminTagsController', 'abstract_add'))
                {{ AdminTagsController::abstract_add(@$place->tags()) }}
            @endif
     */
    /*
    ### PROTOTYPE, please use a ExtForm functional ###
	public static function abstract_add($tags = array(), $options = array()){
        
        $tpl = "extform_tags";
        if (@$options['tpl']) {
            $tpl = $options['tpl'];
            unset($options['tpl']);
        }
        
        if (is_object($tags)) {
            $temp = array();
            foreach ($tags as $t => $tag) {
                #$temp[] = array(
                #    'id'         => $tag->id,
                #    'module'     => $tag->module,
                #    'unit_id'    => $tag->unit_id,
                #    'tag'        => $tag->tag,
                #    'created_at' => $tag->created_at,
                #    'updated_at' => $tag->updated_at,
                #);
                $temp[] = $tag->tag;
            }
            $tags = $temp;
            unset($temp);
        }
        
        if (is_array($tags))
            $tags = implode(",", $tags);
        #dd(View::make(static::returnTpl().$tpl));
        return View::make(static::returnTpl().$tpl, compact('tags', 'options'));
	}
    */
}

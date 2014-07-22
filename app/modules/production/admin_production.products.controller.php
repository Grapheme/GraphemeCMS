<?php

class AdminProductionProductsController extends BaseController {

    public static $name = 'products';
    public static $group = 'production';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {

        $class = __CLASS__;
        Route::group(array('before' => 'auth', 'prefix' => $prefix), function() use ($class) {
        	Route::controller($class::$group."/".$class::$name, $class);
        });
    }

    ## Extended Form elements of module
    public static function returnExtFormElements() {

        $mod_tpl = static::returnTpl();
        $class = __CLASS__;

        ##
        ## EXTFORM GALLERY
        ##
        /*
        ################################################
        ## Process gallery
        ################################################
        if (Allow::action('admin_galleries', 'edit')) {
            ExtForm::process('gallery', array(
                'module'  => self::$name,
                'unit_id' => $id,
                'gallery' => Input::get('gallery'),
            ));
        }
        ################################################
        */
    	ExtForm::add(
            ## Name of element
            "production_product",
            ## Closure for templates (html-code)
            function($name = 'product', $value = '', $params = null) use ($mod_tpl, $class) {
                #dd($params);
                #dd($value);

                ## default template
                $tpl = "extform_product";
                ## custom template
                if (@$params['tpl']) {
                    $tpl = $params['tpl'];
                    unset($params['tpl']);
                }

                ## if gettin' value - is Eloquent object
                ## make array
                $products = array();
                $arr = array();
                $categories = ProductCategory::all();
                if (is_object($categories)) {
                    $temp = array();
                    $temp[] = 'Выберите продукт';
                    foreach ($categories as $c => $cat) {
                        $products = $cat->products();
                        if (is_object($products) && count($products)) {
                            $arr = array();
                            #Helper::d($temp);
                            foreach ($products as $key => $val)
                                $arr[$val->id] = "   " . $val->title;
                            $temp[$cat->title] = $arr;
                        }
                    }
                    $products = $temp;
                    unset($temp);
                }

                #Helper::d($products);
                #$products = array();

                if (is_object($value)) {
                    $value = $value->id;
                }

                #Helper::dd($value);

                return View::make($mod_tpl.$tpl, compact('name', 'value', 'products', 'params'));
    	    },
            ## Processing results closure
            function($params) use ($mod_tpl, $class) {
                #Helper::dd($params);
                ## Array with POST-data
                $product_id = isset($params['product']) ? $params['product'] : false;
                if (!$product_id)
                    return false;
                ## Return format
                $return = isset($params['return']) ? $params['return'] : false;
                ## Find product by ID
                $product = Product::where('id', $product_id)->first();
                ## If product doesn't exists - return false
                if (is_null($product))
                    return false;
                ## Return needable property or full object
                return $return ? @$product->$return : $product;

            }
        );

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

        $limit = 30;

        Allow::permission($this->module['group'], 'product_view');

        $category = ProductCategory::where('id', Input::get('cat'))->first();
        $categories = ProductCategory::all();

        $cat = Input::get('cat');
		$products = new Product;
        $products = is_numeric($cat) ? $products->where('category_id', $cat)->paginate($limit) : $products->paginate($limit);

		return View::make($this->module['tpl'].'index', compact('products', 'categories', 'cat', 'category'));
	}

    /****************************************************************************/

	public function getCreate(){

        Allow::permission($this->module['group'], 'product_create');

        $cat = Input::get('cat');

        $categories = array('Выберите категорию');
        $temp = ProductCategory::all();
        foreach ($temp as $tmp) {
            $categories[$tmp->id] = $tmp->title;
        }

		return View::make($this->module['tpl'].'create', compact('categories', 'cat'));
	}

	public function postStore(){

        Allow::permission($this->module['group'], 'product_create');

		$json_request = array('status'=>FALSE, 'responseText'=>'', 'responseErrorText'=>'', 'redirect'=>FALSE);

		$input = array(
            'title' => Input::get('title'),
            'link' => Input::get('link'),
            'category_id' => Input::get('category_id'),
            'short' => Input::get('short'),
            'desc' => Input::get('desc'),
        );
        ################################################
        ## Process image
        ################################################
        if (Allow::action('galleries', 'edit')) {
            $image_id = ExtForm::process('image', array(
                'image' => Input::get('image'),
                'return' => 'id'
            ));
            $input['image_id'] = $image_id;
        }
        ################################################

		$validation = Validator::make($input, Product::$rules);
		if($validation->passes()) {

			Product::create($input);
			#return link::auth('groups');

			$json_request['responseText'] = "Продукт создан создана";
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

        Allow::permission($this->module['group'], 'product_edit');

		$product = Product::find($id);

        $categories = array('Выберите категорию');
        $temp = ProductCategory::all();
        foreach ($temp as $tmp) {
            $categories[$tmp->id] = $tmp->title;
        }

		return View::make($this->module['tpl'].'edit', compact('product', 'categories'));
	}

	public function postUpdate($id){

        Allow::permission($this->module['group'], 'product_edit');

		$json_request = array('status'=>FALSE, 'responseText'=>'', 'responseErrorText'=>'', 'redirect'=>FALSE);
		if(!Request::ajax())
            return App::abort(404);

		if(!$product = Product::find($id)) {
			$json_request['responseText'] = 'Запрашиваемый продукт не найден!';
			return Response::json($json_request, 400);
		}

        $input = array(
            'title' => Input::get('title'),
            'link' => Input::get('link'),
            'category_id' => Input::get('category_id'),
            'short' => Input::get('short'),
            'desc' => Input::get('desc'),
        );
        ################################################
        ## Process image
        ################################################
        if (Allow::action('galleries', 'edit')) {
            $image_id = ExtForm::process('image', array(
                'image' => Input::get('image'),
                'return' => 'id'
            ));
            $input['image_id'] = $image_id;
        }
        ################################################

		$validation = Validator::make($input, Product::$rules);
		if($validation->passes()):

			$product->update($input);

			$json_request['responseText'] = 'Продукт обновлен';
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

        Allow::permission($this->module['group'], 'product_delete');

		if(!Request::ajax())
            return App::abort(404);

		$json_request = array('status'=>FALSE, 'responseText'=>'');

	    $product = Product::find($id);
        if($image = $product->images()->first()):
            if (!empty($image->name) && File::exists(public_path('uploads/galleries/thumbs/'.$image->name))):
                File::delete(public_path('uploads/galleries/thumbs/'.$image->name));
                File::delete(public_path('uploads/galleries/'.$image->name));
                Photo::find($image->id)->delete();
            endif;
        endif;
        $product->delete();
		$json_request['responseText'] = 'Продукт удален';
		$json_request['status'] = TRUE;
		return Response::json($json_request, 200);
	}

}

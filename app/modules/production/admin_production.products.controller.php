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

    protected $product;
    public $locales;

	public function __construct(Product $product){

        $this->product = $product;
		#$this->beforeFilter('groups');
        $this->locales = Config::get('app.locales');

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
        $products = is_numeric($cat) ? $this->product->where('category_id', $cat)->with('meta')->paginate($limit) : $this->product->with('meta')->paginate($limit);
		return View::make($this->module['tpl'].'index', compact('products', 'categories', 'cat', 'category'));
	}

    /****************************************************************************/

	public function getCreate(){

        Allow::permission($this->module['group'], 'product_create');
        $cat = Input::get('cat');
        $categories = array('Выберите категорию');
        foreach (ProductCategory::all() as $category):
            $categories[$category->id] = $category->title;
        endforeach;
        $locales = $this->locales;
		return View::make($this->module['tpl'].'create', compact('categories', 'cat','locales'));
	}

	public function postStore(){

        if(!Request::ajax()) return App::abort(404);
        Allow::permission($this->module['group'], 'product_create');
		$json_request = array('status'=>FALSE, 'responseText'=>'', 'responseErrorText'=>'', 'redirect'=>FALSE, 'gallery'=>0);
		$validation = Validator::make(Input::all(), Product::$rules);
		if($validation->passes()) {
            self::saveProductModel();
            self::saveProductsMetaModel();
            $json_request['gallery'] = self::saveProductsGallery();
			$json_request['responseText'] = "Продукт создан";
			$json_request['redirect'] = link::auth($this->module['rest']);
			$json_request['status'] = TRUE;
		} else {
			$json_request['responseText'] = 'Неверно заполнены поля';
			$json_request['responseErrorText'] = implode($validation->messages()->all(),'<br />');
		}
		return Response::json($json_request, 200);
	}

    /****************************************************************************/

	public function getEdit($id){

        Allow::permission($this->module['group'], 'product_edit');
        $product = $this->product->where('id',$id)->with('meta')->with('images')->with(array('gallery'=>function($query) use ($id){
            $query->where('module',self::$name);
            $query->where('unit_id',$id);
        }))->first();
        $categories = array('Выберите категорию');
        foreach (ProductCategory::all() as $category):
            $categories[$category->id] = $category->title;
        endforeach;
        $locales = $this->locales;
		return View::make($this->module['tpl'].'edit', compact('product', 'categories','locales'));
	}

	public function postUpdate($id){

        if(!Request::ajax()) return App::abort(404);
        Allow::permission($this->module['group'], 'product_edit');
		$json_request = array('status'=>FALSE, 'responseText'=>'', 'responseErrorText'=>'', 'redirect'=>FALSE, 'gallery'=>0);
		$validation = Validator::make(Input::all(), Product::$rules);
		if($validation->passes()):
            $product = $this->product->find($id);
            self::saveProductModel($product);
            self::saveProductsMetaModel();
            $json_request['gallery'] = self::saveProductsGallery();
			$json_request['responseText'] = 'Продукт обновлен';
			$json_request['status'] = TRUE;
		else:
			$json_request['responseText'] = 'Неверно заполнены поля';
			$json_request['responseErrorText'] = implode($validation->messages()->all(), '<br />');
		endif;

		return Response::json($json_request, 200);
	}

    /****************************************************************************/

	public function deleteDestroy($id){

        if(!Request::ajax()) return App::abort(404);
        Allow::permission($this->module['group'], 'product_delete');
        $json_request = array('status'=>FALSE, 'responseText'=>'');
        if(Request::ajax()):
            $product = $this->product->find($id);
            if($image = $product->images()->first()):
                if (!empty($image->name) && File::exists(public_path('uploads/galleries/thumbs/'.$image->name))):
                    File::delete(public_path('uploads/galleries/thumbs/'.$image->name));
                    File::delete(public_path('uploads/galleries/'.$image->name));
                    Photo::find($image->id)->delete();
                endif;
            endif;
            $product->meta()->delete();
            $product->delete();
            $json_request['responseText'] = 'Продукт удален';
            $json_request['status'] = TRUE;
        else:
            return App::abort(404);
        endif;
        return Response::json($json_request,200);
	}

    private function saveProductModel($product = NULL){

        if(is_null($product)):
            $product = $this->product;
        endif;

        $product->category_id = Input::get('category_id');
        $product->publication = 1;
        $product->image_id =  Input::get('image');
        $product->gallery_id =  Input::get('gallery_id');

        ## Сохраняем в БД
        $product->save();
        $product->touch();

        $this->product = $product;
        return TRUE;
    }

    private function saveProductsMetaModel(){

        foreach($this->locales as $locale):
            if (!$productMeta = ProductsMeta::where('product_id',$this->product->id)->where('language',$locale)->first()):
                $productMeta = new ProductsMeta;
            endif;
            $productMeta->product_id = $this->product->id;
            $productMeta->language = $locale;
            $productMeta->title = Input::get('title.' . $locale);
            $productMeta->short_title = Input::get('short_title.' . $locale);
            $productMeta->price = Input::get('price.' . $locale);
            $productMeta->preview = Input::get('preview.' . $locale);
            $productMeta->content = Input::get('content.' . $locale);

            /*
           $eventMeta->seo_url = Input::get('seo_url.' . $locale);
           $eventMeta->seo_title = Input::get('seo_title.' . $locale);
           $eventMeta->seo_description = Input::get('seo_description.' . $locale);
           $eventMeta->seo_keywords = Input::get('seo_keywords.' . $locale);
           $eventMeta->seo_h1 = Input::get('seo_h1.' . $locale);
           */

            if(Allow::enabled_module('seo')):
                if(is_null(Input::get('seo_url.' . $locale))):
                    $productMeta->seo_url = '';
                elseif(Input::get('seo_url.' . $locale) === ''):
                    $productMeta->seo_url = $this->stringTranslite(Input::get('title.' . $locale));
                else:
                    $productMeta->seo_url = $this->stringTranslite(Input::get('seo_url.' . $locale));
                endif;
                $productMeta->seo_url = (string)$productMeta->seo_url;
                if(Input::get('seo_title.' . $locale) == ''):
                    $productMeta->seo_title = $productMeta->title;
                else:
                    $productMeta->seo_title = trim(Input::get('seo_title.' . $locale));
                endif;
                $productMeta->seo_description = Input::get('seo_description.' . $locale);
                $productMeta->seo_keywords = Input::get('seo_keywords.' . $locale);
                $productMeta->seo_h1 = Input::get('seo_h1.' . $locale);
            else:
                $productMeta->seo_url = $this->stringTranslite(Input::get('title.' . $locale));
                $productMeta->seo_title = Input::get('title.' . $locale);
                $productMeta->seo_description = $productMeta->seo_keywords = $productMeta->seo_h1 = '';
            endif;

            $productMeta->save();
            $productMeta->touch();

        endforeach;
        return TRUE;
    }

    private function saveProductsGallery(){

        $gallery_id = FALSE;
        if(Allow::action('admin_galleries','edit')):
            $gallery_id = ExtForm::process('gallery', array(
                'module'  => 'products',
                'unit_id' => $this->product->id,
                'gallery' => Input::get('gallery'),
            ));
        endif;
        return $gallery_id;
    }
}

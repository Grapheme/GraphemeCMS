<?php

class AdminNewsController extends BaseController {

    public static $name = 'news';
    public static $group = 'news';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {
        $class = __CLASS__;
        Route::group(array('before' => 'auth', 'prefix' => $prefix), function() use ($class) {
        	Route::controller($class::$group, $class);
        });
    }

    ## Shortcodes of module
    public static function returnShortCodes() {

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
        	'title' => 'Новости',
            'visible' => 1,
        );
    }

    ## Menu elements of the module
    public static function returnMenu() {
        return array(
            array(
            	'title' => 'Новости',
                'link' => self::$group,
                'class' => 'fa-calendar', 
                'permit' => 'view',
            ),
        );
    }

    /****************************************************************************/

	protected $news;

	public function __construct(I18nNews $news){

		$this->news = $news;
		$this->beforeFilter('news');
        $this->locales = Config::get('app.locales');

        View::share('module_name', self::$name);

        $this->tpl = static::returnTpl('admin');
        $this->gtpl = static::returnTpl();
        View::share('module_tpl', $this->tpl);
        View::share('module_gtpl', $this->gtpl);

	}

	public function getIndex(){

		$news = $this->news->orderBy('published_at', 'DESC')->with('meta')->get();
		return View::make($this->tpl.'index',array('news'=> $news,'locales' => $this->locales));
	}

	public function getCreate(){

		$this->moduleActionPermission('news','create');
		return View::make($this->tpl.'create', array('locales' => $this->locales,'templates'=>$this->templates(__DIR__)));
	}

	public function postStore(){

		$this->moduleActionPermission('news','create');
		$json_request = array('status'=>FALSE,'responseText'=>'','responseErrorText'=>'','redirect'=>FALSE);
		if(Request::ajax()):
			$validator = Validator::make(Input::all(), I18nNews::$rules);
			if($validator->passes()):
				self::saveNewsModel();
				$json_request['responseText'] = 'Новость создана';
				$json_request['redirect'] = link::auth('news');
				$json_request['status'] = TRUE;
			else:
				$json_request['responseText'] = 'Неверно заполнены поля';
				$json_request['responseErrorText'] = $validator->messages()->all();
			endif;
		else:
			return App::abort(404);
		endif;
		return Response::json($json_request,200);
	}

	public function getEdit($id){

        $this->moduleActionPermission('news','edit');
        if(!$news = $this->news->find($id)->with('meta')->with('images')->first()):
            return App::abort(404);
        endif;
		$gall = Rel_mod_gallery::where('module', 'news')->where('unit_id', $id)->first();
		return View::make($this->tpl.'edit', array('news'=>$news, 'locales' => $this->locales, 'gall' => $gall));
	}

	public function postUpdate($id){

		$this->moduleActionPermission('news','edit');
		$json_request = array('status'=>FALSE,'responseText'=>'','responseErrorText'=>'','redirect'=>FALSE);
		if(Request::ajax()):
			$validator = Validator::make(Input::all(), I18nNews::$rules);
			if($validator->passes()):

			    #$json_request['responseText'] = "<pre>" . print_r($_POST, 1) . "</pre>";
			    #return Response::json($json_request,200);

				$news = $this->news->find($id);
				self::saveNewsModel($news);
				$json_request['responseText'] = 'Новость сохранена';
				$json_request['redirect'] = link::auth('news');
				$json_request['status'] = TRUE;
			else:
				$json_request['responseText'] = 'Неверно заполнены поля';
				$json_request['responseErrorText'] = $validator->messages()->all();
			endif;
		else:
			return App::abort(404);
		endif;
		return Response::json($json_request,200);
	}

	public function deleteDestroy($id){

        $this->moduleActionPermission('news', 'delete');
        $json_request = array('status'=>FALSE, 'responseText'=>'');
        if(Request::ajax()):
            $news = $this->news->find($id);
            if($image = $news->images()->first()):
                if (!empty($image->name) && File::exists(public_path('uploads/galleries/thumbs/'.$image->name))):
                    File::delete(public_path('uploads/galleries/thumbs/'.$image->name));
                    File::delete(public_path('uploads/galleries/'.$image->name));
                    Photo::find($image->id)->delete();
                endif;
            endif;
            $news->meta()->delete();
            $news->delete();
            $json_request['responseText'] = 'Новость удалена';
            $json_request['status'] = TRUE;
        #endif;
        else:
            return App::abort(404);
        endif;
        return Response::json($json_request,200);
	}

	private function saveNewsModel($news = NULL){

		if(is_null($news)):
			$news = $this->news;
		endif;

        ## Собираем значения объекта
		if(Allow::enabled_module('templates')):
			$news->template = Input::get('template');
		else:
			$news->template = 'default';
		endif;
		$news->publication = 1;
		$news->published_at = date("Y-m-d", strtotime(Input::get('published_at')));
		$news->slug = Input::get('slug');
        $news->image_id =  Input::get('image');
		## Сохраняем в БД
        $news->save();
		$news->touch();

		## Работа с загруженными изображениями
		$images = @Input::get('uploaded_images');
		$gallery_id = @Input::get('gallery_id');
		if (@count($images)) :
			GalleriesController::imagesToUnit($images, 'news', $news->id, $gallery_id);
		endif;

        self::saveNewsMetaModel($news);
		return $news;
	}

    private function saveNewsMetaModel($news = NULL){

        foreach($this->locales as $locale):
            if (!$newsMeta = I18nNewsMeta::where('news_id',$news->id)->where('language',$locale)->first()):
                $newsMeta = new I18nNewsMeta;
            endif;
            $newsMeta->news_id = $news->id;
            $newsMeta->language = $locale;
            $newsMeta->title = Input::get('title.' . $locale);
            $newsMeta->content = Input::get('content.' . $locale);
            $newsMeta->preview = Input::get('preview.' . $locale);

            /*
           $newsMeta->seo_url = Input::get('seo_url.' . $locale);
           $newsMeta->seo_title = Input::get('seo_title.' . $locale);
           $newsMeta->seo_description = Input::get('seo_description.' . $locale);
           $newsMeta->seo_keywords = Input::get('seo_keywords.' . $locale);
           $newsMeta->seo_h1 = Input::get('seo_h1.' . $locale);
           */

            if(Allow::enabled_module('seo')):
                if(is_null(Input::get('seo_url.' . $locale))):
                    $newsMeta->seo_url = '';
                elseif(Input::get('seo_url.' . $locale) === ''):
                    $newsMeta->seo_url = $this->stringTranslite(Input::get('title.' . $locale));
                else:
                    $newsMeta->seo_url = $this->stringTranslite(Input::get('seo_url.' . $locale));
                endif;
                $newsMeta->seo_url = (string)$newsMeta->seo_url;
                if(Input::get('seo_title.' . $locale) == ''):
                    $newsMeta->seo_title = $newsMeta->title;
                else:
                    $newsMeta->seo_title = trim(Input::get('seo_title.' . $locale));
                endif;
                $newsMeta->seo_description = Input::get('seo_description.' . $locale);
                $newsMeta->seo_keywords = Input::get('seo_keywords.' . $locale);
                $newsMeta->seo_h1 = Input::get('seo_h1.' . $locale);
            else:
                $newsMeta->seo_url = $this->stringTranslite(Input::get('title.' . $locale));
                $newsMeta->seo_title = Input::get('title.' . $locale);
                $newsMeta->seo_description = $newsMeta->seo_keywords = $newsMeta->seo_h1 = '';
            endif;

            $newsMeta->save();
            $newsMeta->touch();
        endforeach;
        return TRUE;
    }


}



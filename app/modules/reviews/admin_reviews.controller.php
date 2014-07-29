<?php

class AdminReviewsController extends BaseController {

    public static $name = 'reviews';
    public static $group = 'reviews';

    /****************************************************************************/
    ## Routing rules of module
    public static function returnRoutes($prefix = null) {
        $class = __CLASS__;
        Route::group(array('before' => 'auth', 'prefix' => $prefix), function() use ($class) {
            Route::controller($class::$group, $class,array('except'=>'show'));
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
            'title' => 'Отзывы',
            'visible' => 1,
        );
    }

    ## Menu elements of the module
    public static function returnMenu() {
        return array(
            array(
                'title' => 'Отзывы',
                'link' => self::$group,
                'class' => 'fa-comments-o',
                'permit' => 'view',
            ),
        );
    }

    /****************************************************************************/
    protected $review;

    public function __construct(Reviews $review){

        $this->review = $review;
        $this->beforeFilter('reviews');
        $this->locales = Config::get('app.locales');

        View::share('module_name', self::$name);

        $this->tpl = static::returnTpl('admin');
        $this->gtpl = static::returnTpl();
        View::share('module_tpl', $this->tpl);
        View::share('module_gtpl', $this->gtpl);

    }

    public function getIndex(){

        $reviews = $this->review->orderBy('published_at', 'DESC')->orderBy('id','DESC')->with('meta')->get();
        return View::make($this->tpl.'index', array('reviews' => $reviews, 'locales' => $this->locales));
    }

    public function getCreate(){

        $this->moduleActionPermission('reviews','create');
        return View::make($this->tpl.'create', array('locales' => $this->locales,'templates'=>$this->templates(__DIR__)));
    }

    public function postStore(){

        $this->moduleActionPermission('reviews','create');
        $json_request = array('status'=>FALSE,'responseText'=>'','responseErrorText'=>'','redirect'=>FALSE);
        if(Request::ajax()):
            $validator = Validator::make(Input::all(), Reviews::$rules);
            if($validator->passes()):
                self::saveReviewModel();
                $json_request['responseText'] = 'Отзыв создан';
                $json_request['redirect'] = link::auth('reviews');
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

        $this->moduleActionPermission('reviews','edit');
        if(!$review = $this->review->where('id',$id)->with('meta')->with('images')->first()):
            return App::abort(404);
        endif;
        $gall = Rel_mod_gallery::where('module','reviews')->where('unit_id', $id)->first();
        return View::make($this->tpl.'edit', array('review'=>$review, 'locales' => $this->locales, 'gall' => $gall));
    }

    public function postUpdate($id){

        $this->moduleActionPermission('reviews','edit');
        $json_request = array('status'=>FALSE,'responseText'=>'','responseErrorText'=>'','redirect'=>FALSE);
        if(Request::ajax()):
            $validator = Validator::make(Input::all(), Reviews::$rules);
            if($validator->passes()):
                $review = $this->review->find($id);
                self::saveReviewModel($review);
                $json_request['responseText'] = 'Отзыв сохранен';
                $json_request['redirect'] = link::auth('reviews');
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

        $this->moduleActionPermission('reviews', 'delete');
        $json_request = array('status'=>FALSE, 'responseText'=>'');
        if(Request::ajax()):
            $review = $this->review->find($id);
            if($image = $review->images()->first()):
                if (!empty($image->name) && File::exists(public_path('uploads/galleries/thumbs/'.$image->name))):
                    File::delete(public_path('uploads/galleries/thumbs/'.$image->name));
                    File::delete(public_path('uploads/galleries/'.$image->name));
                    Photo::find($image->id)->delete();
                endif;
            endif;
            $review->meta()->delete();
            $review->delete();
            $json_request['responseText'] = 'Отзыв удален';
            $json_request['status'] = TRUE;
        #endif;
        else:
            return App::abort(404);
        endif;
        return Response::json($json_request,200);
    }

    private function saveReviewModel($review = NULL){

        if(is_null($review)):
            $review = $this->review;
        endif;

        ## Собираем значения объекта
        if(Allow::enabled_module('templates')):
            $review->template = Input::get('template');
        else:
            $review->template = 'default';
        endif;
        $review->slug = Input::get('slug');
        $review->publication = 1;
        $review->published_at = date("Y-m-d", strtotime(Input::get('published_at')));
        $review->image_id =  Input::get('image');

        ## Сохраняем в БД
        $review->save();
        $review->touch();
        self::saveReviewMetaModel($review);
        return $review->id;
    }

    private function saveReviewMetaModel($review = NULL){

        foreach($this->locales as $locale):
            if (!$reviewMeta = ReviewsMeta::where('review_id',$review->id)->where('language',$locale)->first()):
                $reviewMeta = new ReviewsMeta;
            endif;
            $reviewMeta->review_id = $review->id;
            $reviewMeta->language = $locale;
            $reviewMeta->name = Input::get('name.' . $locale);
            $reviewMeta->position = Input::get('position.' . $locale);
            $reviewMeta->preview = Input::get('preview.' . $locale);
            $reviewMeta->content = Input::get('content.' . $locale);

            /*
           $eventMeta->seo_url = Input::get('seo_url.' . $locale);
           $eventMeta->seo_title = Input::get('seo_title.' . $locale);
           $eventMeta->seo_description = Input::get('seo_description.' . $locale);
           $eventMeta->seo_keywords = Input::get('seo_keywords.' . $locale);
           $eventMeta->seo_h1 = Input::get('seo_h1.' . $locale);
           */

            if(Allow::enabled_module('seo')):
                if(is_null(Input::get('seo_url.' . $locale))):
                    $reviewMeta->seo_url = '';
                elseif(Input::get('seo_url.' . $locale) === ''):
                    $reviewMeta->seo_url = $this->stringTranslite(Input::get('name' . $locale));
                else:
                    $reviewMeta->seo_url = $this->stringTranslite(Input::get('seo_url.' . $locale));
                endif;
                $reviewMeta->seo_url = (string)$reviewMeta->seo_url;
                if(Input::get('seo_title.' . $locale) == ''):
                    $reviewMeta->seo_title = $reviewMeta->title;
                else:
                    $reviewMeta->seo_title = trim(Input::get('seo_title.' . $locale));
                endif;
                $reviewMeta->seo_description = Input::get('seo_description.' . $locale);
                $reviewMeta->seo_keywords = Input::get('seo_keywords.' . $locale);
                $reviewMeta->seo_h1 = Input::get('seo_h1.' . $locale);
            else:
                $reviewMeta->seo_url = $this->stringTranslite(Input::get('name.' . $locale));
                $reviewMeta->seo_title = Input::get('name.' . $locale);
                $reviewMeta->seo_description = $reviewMeta->seo_keywords = $reviewMeta->seo_h1 = '';
            endif;

            $reviewMeta->save();
            $reviewMeta->touch();
        endforeach;
        return TRUE;
    }
}
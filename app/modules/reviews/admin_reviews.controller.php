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

        $reviews = $this->review->orderBy('published_at', 'DESC')->orderBy('id','DESC')->get();
        return View::make($this->tpl.'index', array('reviews' => $reviews, 'locales' => $this->locales));
    }

    public function getCreate(){

        $this->moduleActionPermission('reviews','create');
        return View::make($this->tpl.'create', array('locales' => $this->locales));
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
        if(!$review = $this->review->find($id)):
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

        $this->moduleActionPermission('news', 'delete');
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
        $review->publication = 1;
        $review->published_at = date("Y-m-d", strtotime(Input::get('published_at')));
        $slug = Input::get('slug');

        $locale = Config::get('app.locale');

        $review->name = Input::get('name.'.$locale);
        $review->position = Input::get('position.'.$locale);
        $review->content = Input::get('content.'.$locale);
        $review->slug = $slug;
        $review->image_id =  Input::get('image');
        ## Сохраняем в БД
        $review->save();
        $review->touch();
        return $review->id;
    }

}
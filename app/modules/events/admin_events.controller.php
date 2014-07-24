<?php

class AdminEventsController extends BaseController {

    public static $name = 'events';
    public static $group = 'events';

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
            'title' => 'События',
            'visible' => 1,
        );
    }

    ## Menu elements of the module
    public static function returnMenu() {
        return array(
            array(
                'title' => 'События',
                'link' => self::$group,
                'class' => ' fa-flag-o',
                'permit' => 'view',
            ),
        );
    }

    /****************************************************************************/
    protected $event;

    public function __construct(Events $event){

        $this->event = $event;
        $this->beforeFilter('events');
        $this->locales = Config::get('app.locales');

        View::share('module_name', self::$name);

        $this->tpl = static::returnTpl('admin');
        $this->gtpl = static::returnTpl();
        View::share('module_tpl', $this->tpl);
        View::share('module_gtpl', $this->gtpl);

    }

    public function getIndex(){

        $events = $this->event->orderBy('published_at', 'DESC')->orderBy('id','DESC')->with('meta')->get();
        return View::make($this->tpl.'index', array('events' => $events, 'locales' => $this->locales));
    }

    public function getCreate(){

        $this->moduleActionPermission('events','create');
        return View::make($this->tpl.'create', array('locales' => $this->locales,'templates'=>$this->templates(__DIR__)));
    }

    public function postStore(){

        $this->moduleActionPermission('events','create');
        $json_request = array('status'=>FALSE,'responseText'=>'','responseErrorText'=>'','redirect'=>FALSE);
        if(Request::ajax()):
            $validator = Validator::make(Input::all(), Events::$rules);
            if($validator->passes()):
                self::saveEventsModel();
                $json_request['responseText'] = 'Событие создано';
                $json_request['redirect'] = link::auth('events');
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

        $this->moduleActionPermission('events','edit');
        if(!$event = $this->event->find($id)->with('meta')->with('images')->first()):
            return App::abort(404);
        endif;
        $gall = Rel_mod_gallery::where('module','events')->where('unit_id', $id)->first();
        return View::make($this->tpl.'edit', array('event'=>$event, 'locales' => $this->locales, 'gall' => $gall,'templates'=>$this->templates(__DIR__)));
    }

    public function postUpdate($id){

        $this->moduleActionPermission('events','edit');
        $json_request = array('status'=>FALSE,'responseText'=>'','responseErrorText'=>'','redirect'=>FALSE);
        if(Request::ajax()):
            $validator = Validator::make(Input::all(), Events::$rules);
            if($validator->passes()):
                $event = $this->event->find($id);
                self::saveEventsModel($event);
                self::saveEventsMetaModel($event);
                $json_request['responseText'] = 'Событие сохранено';
                $json_request['redirect'] = link::auth('events');
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

        $this->moduleActionPermission('events', 'delete');
        $json_request = array('status'=>FALSE, 'responseText'=>'');
        if(Request::ajax()):
            $event = $this->event->find($id);
            if($image = $event->images()->first()):
                if (!empty($image->name) && File::exists(public_path('uploads/galleries/thumbs/'.$image->name))):
                    File::delete(public_path('uploads/galleries/thumbs/'.$image->name));
                    File::delete(public_path('uploads/galleries/'.$image->name));
                    Photo::find($image->id)->delete();
                endif;
            endif;
            $event->meta()->delete();
            $event->delete();
            $json_request['responseText'] = 'Событие удалено';
            $json_request['status'] = TRUE;
        #endif;
        else:
            return App::abort(404);
        endif;
        return Response::json($json_request,200);
    }

    private function saveEventsModel($event = NULL){

        if(is_null($event)):
            $event = $this->event;
        endif;
        if(Allow::enabled_module('templates')):
            $event->template = Input::get('template');
        else:
            $event->template = 'default';
        endif;
        $event->slug = Input::get('slug');
        $event->publication = 1;
        $event->published_at = date("Y-m-d", strtotime(Input::get('published_at')));
        $event->image_id =  Input::get('image');

        ## Сохраняем в БД
        $event->save();
        $event->touch();
        self::saveEventsMetaModel($event);
        return $event;
    }

    private function saveEventsMetaModel($event = NULL){

        foreach($this->locales as $locale):
            if (!$eventMeta = EventsMeta::where('event_id',$event->id)->where('language',$locale)->first()):
                $eventMeta = new EventsMeta;
            endif;
            $eventMeta->event_id = $event->id;
            $eventMeta->language = $locale;
            $eventMeta->title = Input::get('title.' . $locale);
            $eventMeta->content = Input::get('content.' . $locale);
            $eventMeta->preview = Input::get('preview.' . $locale);

            /*
           $eventMeta->seo_url = Input::get('seo_url.' . $locale);
           $eventMeta->seo_title = Input::get('seo_title.' . $locale);
           $eventMeta->seo_description = Input::get('seo_description.' . $locale);
           $eventMeta->seo_keywords = Input::get('seo_keywords.' . $locale);
           $eventMeta->seo_h1 = Input::get('seo_h1.' . $locale);
           */

            if(Allow::enabled_module('seo')):
                if(is_null(Input::get('seo_url.' . $locale))):
                    $eventMeta->seo_url = '';
                elseif(Input::get('seo_url.' . $locale) === ''):
                    $eventMeta->seo_url = $this->stringTranslite(Input::get('title.' . $locale));
                else:
                    $eventMeta->seo_url = $this->stringTranslite(Input::get('seo_url.' . $locale));
                endif;
                $eventMeta->seo_url = (string)$eventMeta->seo_url;
                if(Input::get('seo_title.' . $locale) == ''):
                    $eventMeta->seo_title = $eventMeta->title;
                else:
                    $eventMeta->seo_title = trim(Input::get('seo_title.' . $locale));
                endif;
                $eventMeta->seo_description = Input::get('seo_description.' . $locale);
                $eventMeta->seo_keywords = Input::get('seo_keywords.' . $locale);
                $eventMeta->seo_h1 = Input::get('seo_h1.' . $locale);
            else:
                $eventMeta->seo_url = $this->stringTranslite(Input::get('title.' . $locale));
                $eventMeta->seo_title = Input::get('title.' . $locale);
                $eventMeta->seo_description = $eventMeta->seo_keywords = $eventMeta->seo_h1 = '';
            endif;

            $eventMeta->save();
            $eventMeta->touch();
        endforeach;
        return TRUE;
    }

}
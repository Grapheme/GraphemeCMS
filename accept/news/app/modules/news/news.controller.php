<?php

class NewsController extends BaseController {

    public static $name = 'news_public';
    public static $group = 'news';

    public static $prefix_url = 'news';

    public static function returnRoutes($prefix = null) {

        if (self::$prefix_url !== FALSE):
            if (is_array(Config::get('app.locales')) && count(Config::get('app.locales'))) {
                foreach(Config::get('app.locales') as $locale) {
                    Route::group(array('before' => 'i18n_url', 'prefix' => $locale), function(){
                        Route::get('/'.self::$prefix_url.'/{url}', array('as' => 'reviews_full', 'uses' => __CLASS__.'@showFullByUrl'));
                    });
                }
            }
            Route::group(array('before' => 'i18n_url'), function(){
                Route::get('/'.self::$prefix_url.'/{url}', array('as' => 'reviews_full', 'uses' => __CLASS__.'@showFullByUrl'));
            });
        else:
            return NULL;
        endif;

    }
    
    ## Shortcodes of module
    public static function returnShortCodes() {

        $tpl = static::returnTpl();

        shortcode::add("news",

            function($params = array()) use ($tpl) {
                #print_r($params); die;
                ## Gfhfvtnhs по-умолчанию
                $default = array(
                    'tpl' => Config::get('app-default.news_template'),
                    'limit' => Config::get('app-default.news_count_on_page'),
                    'order' => Helper::stringToArray(I18nNews::$order_by),
                    'pagination' => 1,
                );
                ## Применяем переданные настройки
                $params = is_array($params) ? array_merge($default, $params) : $default;

                $locale = Config::get('app.locale');
                $news = I18nNews::orderBy('published_at','desc')->where('publication', 1)->with(array('meta'=>function($query) use ($locale){
                    $query->where('language',$locale);
                    $query->where('title','!=','');
                }))->with('gallery')->with('images')->paginate($params['limit']);
//                        ->paginate($params['limit']); ## news list with pagination

                print_r($news);
                exit;

                if(empty($params['tpl']) || !View::exists($tpl.$params['tpl'])):
                    throw new Exception('Template not found: ' . $tpl.$params['tpl']);
                endif;

                return View::make($tpl.$params['tpl'], compact('news'));
            }
        );
        
    }

    ## Actions of module (for distribution rights of users)
    ## return false;   # for loading default actions from config
    ## return array(); # no rules will be loaded
    /*
    public static function returnActions() {
        return array();
    }
    */

    ## Info about module (now only for admin dashboard & menu)
    /*
    public static function returnInfo() {
    }
    */
    
    /****************************************************************************/

    public function __construct(){

        View::share('module_name', self::$name);

        $this->tpl = $this->gtpl = static::returnTpl();
        View::share('module_tpl', $this->tpl);
        View::share('module_gtpl', $this->gtpl);
    }
    
    /*
    |--------------------------------------------------------------------------
    | Раздел "Новости" - I18N
    |--------------------------------------------------------------------------
    */
    ## Функция для просмотра полной мультиязычной новости
    public function showFullByUrl($url){

        if (!@$url)
            $url = Input::get('url');

        if(!Allow::enabled_module('i18n_news'))
            return App::abort(404);

        $i18n_news = I18nNews::where('slug', $url)->where('publication', 1)->first();

        if (!$i18n_news)
            return App::abort(404);

        if(empty($i18n_news->template) || !View::exists($this->tpl.$i18n_news->template)) {
            #return App::abort(404, 'Отсутствует шаблон: ' . $this->tpl . $i18n_news->template);
            throw new Exception('Template not found: ' . $this->tpl.$i18n_news->template);
        }

        $i18n_news_meta = I18nNewsMeta::where('news_id', $i18n_news->id)->where('language', Config::get('app.locale'))->first();

        if(!$i18n_news_meta || !$i18n_news_meta->title)
            return App::abort(404);

        $gall = Rel_mod_gallery::where('module', 'news')->where('unit_id', $i18n_news->id)->first();

        /**
         * @todo После того, как будет сделано управление модулями (актив/неактив) - поменять условие, активен ли модуль страниц
         */
        if ( method_exists('PagesController', 'content_render') ) {
            $i18n_news_meta->preview = PagesController::content_render($i18n_news_meta->preview);
            $i18n_news_meta->content = PagesController::content_render($i18n_news_meta->content);
        }

        return View::make($this->tpl.$i18n_news->template,
            array(
                'new' => $i18n_news,
                'news'=>$i18n_news_meta,
                'page_title'=>$i18n_news_meta->seo_title,
                'page_description'=>$i18n_news_meta->seo_description,
                'page_keywords'=>$i18n_news_meta->seo_keywords,
                'page_author'=>'',
                'page_h1'=>$i18n_news_meta->seo_h1,
                'menu'=> Page::getMenu('news'),
                'gall' => $gall
            )
        );
    }

}
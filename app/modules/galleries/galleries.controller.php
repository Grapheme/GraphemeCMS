<?php

class GalleriesController extends BaseController {

    public static $name = 'galleries_public';
    public static $group = 'galleries';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {
    }
    
    ## Shortcodes of module
    public static function returnShortCodes() {

        $tpl = static::returnTpl();

    	shortcode::add("gallery",
        
            function($params = null) use ($tpl) {

                $default = array(
                    'tpl' => "gallery-default",
                );
                $params = array_merge($default, (array)$params);

                if(empty($params['tpl']) || !View::exists($tpl.$params['tpl'])) {
                    throw new Exception('Template not found: ' . $tpl.$params['tpl']);
                }

                if (!isset($params['id'])) {
                    #return false;
                    #return "Error: id of gallery is not defined!";
                    throw new Exception('ID of gallery is not defined');
                }

                $gallery_id = $params['id'];

                $gallery = Gallery::where('id', $gallery_id)->first();

                if (!is_object($gallery) || !@$gallery->id) {
                    return false;
    	        	#return "Error: gallery #{$gallery_id} doesn't exist!";
                }
                
                $photos = $gallery->photos;
                
                if (!$photos->count()) {
                    return false;
                }
                
                #dd($tpl.$params['tpl']);
                #dd(compact($photos));
                
                #return View::make($tpl.$params['tpl'], compact($photos)); ## don't work
                return View::make($tpl.$params['tpl'], array('photos' => $photos));
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

	public function __construct(I18nNews $news, I18nNewsMeta $news_meta){

        $this->tpl = static::returnTpl();
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
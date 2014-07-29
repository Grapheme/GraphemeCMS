<?php

class ChannelController extends BaseController {

    public static $name = 'channels';
    public static $group = 'channels';

    /*
     *  Если понадобится создавать страницы для разных категорий елементов каналов тогда
     *  перечислите все префиксы URL для каждой категории $prefix_url = array('prefix_1','prefix_1')
     * и создайте шаблоны с соответствующими именами в каталогие views данного модуля
     */

    public static $prefix_url = array('offer'); # array of FALSE;

    public static function returnRoutes($prefix = null) {

        if (self::$prefix_url !== FALSE):
            if (is_array(Config::get('app.locales')) && count(Config::get('app.locales'))):
                foreach(Config::get('app.locales') as $locale) :
                    Route::group(array('before' => 'i18n_url', 'prefix' => $locale), function(){
                    foreach (self::$prefix_url as $prefix):
                        Route::get('/'.$prefix.'/{url}', array('as' => 'channel_full', 'uses' => __CLASS__.'@showFullByUrl'));
                    endforeach;
                    });
                endforeach;
            endif;
            Route::group(array('before' => 'i18n_url'), function(){
            foreach (self::$prefix_url as $prefix):
                Route::get('/'.$prefix.'/{url}', array('as' => 'channel_full', 'uses' => __CLASS__.'@showFullByUrl'));
            endforeach;
            });
        else:
            return NULL;
        endif;

    }
    
    ## Shortcodes of module
    public static function returnShortCodes() {

        $tpl = static::returnTpl();

    	shortcode::add("channel",
        
            function($params = null) use ($tpl) {
                #print_r($params); die;
        		## Gfhfvtnhs по-умолчанию
                $default = array(
                    'tpl' => Config::get('app-default.news_template'),
                    'limit' => Config::get('app-default.news_count_on_page'),
                    'order' => Helper::stringToArray(I18nNews::$order_by),
                    'pagination' => 1,
                );
        		## Применяем переданные настройки
                $params = array_merge($default, $params);
                #dd($params);

        		#if(Allow::enabled_module('i18n_news')):
        		    ## Получаем новости, делаем LEFT JOIN с news_meta, с проверкой языка и тайтла
        			$selected_news = I18nNews::where('i18n_news.publication', 1)
        			                        ->leftJoin('i18n_news_meta', 'i18n_news_meta.news_id', '=', 'i18n_news.id')
        			                        ->where('i18n_news_meta.language', Config::get('app.locale'))
        			                        ->where('i18n_news_meta.title', '!=', '')
        			                        ->select('*', 'i18n_news.id AS original_id', 'i18n_news.published_at AS created_at')
                                            ->orderBy('i18n_news.published_at', 'desc');
                                            
                    #$selected_news = $selected_news->where('i18n_news_meta.wtitle', '!=', '');

                    ## Получаем новости с учетом пагинации
                    #echo $selected_news->toSql(); die;
                    #var_dump($params['limit']);
        			$news = $selected_news->paginate($params['limit']); ## news list with pagination
        			#$news = $selected_news->get(); ## all news, without pagination

        			foreach ($news as $n => $new) {
        				#print_r($new); die;
        				$gall = Rel_mod_gallery::where('module', 'news')->where('unit_id', $new->original_id)->first();
        				#foreach ($gall->photos as $photo) {
        				#	print_r($photo->path());
        				#}
        				#print_r($gall->photos); die;
        				$new->gall = @$gall;
        				$new->image = is_object(@$gall->photos[0]) ? @$gall->photos[0]->path() : "";
        				$news[$n]->$new;
        			}
        			
                    #echo $news->count(); die;
                    
        			if($news->count()) {

                        #if(empty($params['tpl']) || !View::exists($this->tpl.$params['tpl'])) {
                        if(empty($params['tpl']) || !View::exists($tpl.$params['tpl'])) {
                			#return App::abort(404, 'Отсутствует шаблон: ' . $this->tpl . $i18n_news->template);
        					#return "Отсутствует шаблон: templates.".$params['tpl'];
                            throw new Exception('Template not found: ' . $tpl.$params['tpl']);
                        }

    					return View::make($tpl.$params['tpl'], compact('news'));
        			}
        		#else:
        		#	return '';
        		#endif;
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
    
    ## Функция для просмотра полной мультиязычного элемента канала
    public function showFullByUrl($url = null){

        if (is_null($url)):
            $url = Input::get('url');
        endif;
        if(!Allow::enabled_module('channels')):
            return App::abort(404);
        endif;

        try{
            $element = ChannelCategory::where('slug', Request::segment(1))->with(array('channel' => function ($query) use ($url) {
                    $query->where('link', $url);
                    $query->with('images');
                }))->first();

            if(!$element):
                return App::abort(404);
            endif;
            if(View::exists($this->tpl.$element->slug) === FALSE) :
                throw new Exception('Template not found: '.$this->tpl.$element->slug);
            endif;
            if(method_exists('PagesController','content_render')):
                $element->desc = PagesController::content_render($element->desc);
            endif;
            return View::make($this->tpl.$element->slug,
                array(
                    'page_title' => $element->title.'. '.$element->channel->first()->title,'page_description' => '','page_keywords' => '','page_author' => '',
                    'page_h1' => $element->channel->first()->title,
                    'page_h1' => $element->channel->first()->title,
                    'menu' => NULL,'element' => $element
                )
            );
        }catch (Exception $e){
            return App::abort(404);
        }
	}

}
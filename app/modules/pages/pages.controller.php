<?php

class PagesController extends BaseController {

    public static $name = 'pages_public';
    public static $group = 'pages';

    /****************************************************************************/
    /**
     * @author Alexander Zelensky
     * @todo Возможно, в будущем здесь нужно будет добавить проверку параметра, использовать ли вообще мультиязычность по сегментам урла, или нет. Например, удобно будет отключить мультиязычность по сегментам при использовании разных доменных имен для каждой языковой версии.
     */
    public static function returnRoutes($prefix = null) {
        
        ## УРЛЫ С ЯЗЫКОВЫМИ ПРЕФИКСАМИ ДОЛЖНЫ ИДТИ ПЕРЕД ОБЫЧНЫМИ!
        ## Если в конфиге прописано несколько языковых версий...
        if (is_array(Config::get('app.locales')) && count(Config::get('app.locales'))) {
            ## Для каждого из языков...
            foreach(Config::get('app.locales') as $locale) {
            	## ...генерим роуты с префиксом (первый сегмент), который будет указывать на текущую локаль.
            	## Также указываем before-фильтр i18n_url, для выставления текущей локали.
                Route::group(array('before' => 'i18n_url', 'prefix' => $locale), function(){
                    Route::any('/{url}', array('as' => 'page',     'uses' => __CLASS__.'@showPageByUrl')); ## I18n Pages
                    Route::any('/',      array('as' => 'mainpage', 'uses' => __CLASS__.'@showPageByUrl')); ## I18n Main Page
                });
            }
        }

        ## Генерим роуты без префикса, и назначаем before-фильтр i18n_url.
        ## Это позволяет нам делать редирект на урл с префиксом только для этих роутов, не затрагивая, например, /admin и /login
        Route::group(array('before' => 'i18n_url'), function(){
            
            ## I18n Pages
            Route::any('/{url}', array('as' => 'page', 'uses' => __CLASS__.'@showPageByUrl'));
            ## I18n Main Page
            Route::any('/', array('as' => 'mainpage', 'uses' => __CLASS__.'@showPageByUrl'));

        });
    }
    
    ## Shortcodes of module
    public static function returnShortCodes() {
        /**
         * @todo Сделать шорткод для страниц (вставка страницы внутрь другой страницы, OMG). Да и нужно ли.. Уточнить у Андрея
         */
        #$tpl = static::returnTpl();
    	#shortcode::add("page",
        #    function($params = null) use ($tpl) {}
        #);
    }

    ## Actions of module (for distribution rights of users)
    ## return false;   # for loading $default_actions from config
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

        View::share('module_name', self::$name);

        $this->tpl = $this->gtpl = static::returnTpl();
        View::share('module_tpl', $this->tpl);
        View::share('module_gtpl', $this->gtpl);
	}

    ## Функция для просмотра мультиязычной страницы
    public function showPageByUrl($url = ""){

        if (!@$url)
            $url = Input::get('url');

		if(I18nPage::count() == 0)
			return View::make('guests.welcome');

        ## Страница /de упорно не хотела открываться, пытаясь найти страницу со slug = "de", пришлось сделать вот такой хак:
        if ($url == Config::get('app.locale'))
            $url = '';

        if ($url != '')
    		$page = I18nPage::where('slug', $url)->where('publication', 1)->first();
        else
            $page = I18nPage::where('start_page', '1')->where('publication', 1)->first();

		if(!is_object($page) || !$page->id)
			return App::abort(404);

        ## Если текущая страница - главная, и по какой-то необъяснимой причине у нее задан SLUG - обязательно редиректом юзера на главную страницу для его локали, чтобы не было дублей контента
        if (isset($page->slug) && $page->slug != '' && $page->slug == $url && $page->start_page == 1) {
        	## А чтобы ссылка на главную страницу была правильной - делаем вот такую штуку
        	## Вся соль в том, что если в данный момент текущая локаль - дефолтная, то в slink::createLink() нужно передавать пустую строку. Дефолтная локаль устанавливается равной той же, что и 'app.locale', в файле filters.php
        	$str = Config::get('app.default_locale') == Config::get('app.locale') ? "" : Config::get('app.locale');
	    	Redirect(link::to($str));
        }

        if(empty($page->template) || !View::exists($this->tpl.$page->template)) {
			#return App::abort(404, 'Отсутствует шаблон: ' . $this->tpl . $page->template);
            throw new Exception('Template not found: ' . $this->tpl.$page->template);
        }

		$page_meta = I18nPageMeta::where('page_id',$page->id)->where('language', Config::get('app.locale'))->first();
		if(!is_object($page_meta) || !$page_meta->id)
			return App::abort(404);

        #print_r($page_meta);
        #echo $page->template;

		$content = self::content_render($page_meta->content);
        return View::make(
		    $this->tpl.$page->template,
		    array(
		        'page_title' => $page_meta->seo_title,
		        'page_description' => $page_meta->seo_description,
				'page_keywords' => $page_meta->seo_keywords,
				'page_author' => '',
				'page_h1' => $page_meta->seo_h1,
				'menu' => I18nPage::getMenu($page->template),
				'content' => $content
			)
        );
	}
    

	public static function content_render($page_content, $page_data = NULL){

		$regs = $change = $to = array();
		preg_match_all('~\[([^\]]+?)\]~', $page_content, $matches);

        #dd($page_content);
        #dd($matches);

		for($j=0; $j<count($matches[0]); $j++) {
			$regs[trim($matches[0][$j])] = trim($matches[1][$j]);
		}
        
        #dd($regs);
        
		if(!empty($regs)) {
			foreach($regs as $tochange => $clear):
                
                #echo "$tochange => $clear"; die;
                
				if(!empty($clear)):
					$change[] = $tochange;
					$tag = explode(' ', $clear);

                    #dd($tag);
                    
					if(isset($tag[0]) && $tag[0] == 'view') {
						$to[] = self::shortcode($clear, $page_data);
					} else {
						$to[] = self::shortcode($clear);
					}
				endif;
			endforeach;
		}
        
        #dd($change);
        
		return str_replace($change, $to, $page_content);
	}

	private static function shortcode($clear, $data = NULL){

        ## $clear - строка шорткода без квадратных скобок []
        #dd($clear);

		$str = explode(" ", $clear);
		#$type = $str[0]; ## name of shortcode
        $type = array_shift($str);
		$options = NULL;
		if(count($str)) {
			#for($i=1; $i<count($str); $i++) {
            foreach ($str as $expr) {
                if (!strpos($expr, "="))
                    continue;
				#preg_match_all("~([^\=]+?)=['\"]([^'\"\s\t\r\n]+?)['\"]~", $str[$i], $rendered);
                #dd($rendered);
                list($key, $value) = explode("=", $expr);
                $key = trim($key);
                $value = trim($value, "'\"");
				if($key != '' && $value != '') {
					$options[$key] = $value;
				}
			}
		}

        #dd($type);
        #dd($options);

		return shortcode::run($type, $options);
	}

}
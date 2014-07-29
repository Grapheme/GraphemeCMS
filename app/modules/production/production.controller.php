<?php

class ProductionController extends BaseController {

    public static $name = 'products_public';
    public static $group = 'production';
    public $template = 'product';

    public static $prefix_url = 'catalog';
    /****************************************************************************/
    /**
     * @author Alexander Zelensky
     * @todo Возможно, в будущем здесь нужно будет добавить проверку параметра, использовать ли вообще мультиязычность по сегментам урла, или нет. Например, удобно будет отключить мультиязычность по сегментам при использовании разных доменных имен для каждой языковой версии.
     */
    ## Routing rules of module
    public static function returnRoutes($prefix = null) {

        ## УРЛЫ С ЯЗЫКОВЫМИ ПРЕФИКСАМИ ДОЛЖНЫ ИДТИ ПЕРЕД ОБЫЧНЫМИ!
        ## Если в конфиге прописано несколько языковых версий...
        if (is_array(Config::get('app.locales')) && count(Config::get('app.locales'))) {
            ## Для каждого из языков...
            foreach(Config::get('app.locales') as $locale) {
                ## ...генерим роуты с префиксом (первый сегмент), который будет указывать на текущую локаль.
                ## Также указываем before-фильтр i18n_url, для выставления текущей локали.
                Route::group(array('before' => 'i18n_url', 'prefix' => $locale), function(){
                    Route::get('/'.self::$prefix_url.'/{url}', array('as' => 'single_product', 'uses' => __CLASS__.'@showProduct')); ## I18n Production
                });
            }
        }

        ## Генерим роуты без префикса, и назначаем before-фильтр i18n_url.
        ## Это позволяет нам делать редирект на урл с префиксом только для этих роутов, не затрагивая, например, /admin и /login
        Route::group(array('before' => 'i18n_url'), function(){
            Route::get('/'.self::$prefix_url.'/{url}', array('as' => 'single_product', 'uses' => __CLASS__.'@showProduct'
                #function($url) {
                #	return $this->showFullByUrl($url);
                #}
            )); ## I18n News
        });
    }

    ## Shortcodes of module
    public static function returnShortCodes() {

    }

    /****************************************************************************/

    public function __construct(Product $product){

        View::share('module_name', self::$name);

        $this->tpl = $this->gtpl = static::returnTpl();
        View::share('module_tpl', $this->tpl);
        View::share('module_gtpl', $this->gtpl);
    }

    /*
    |--------------------------------------------------------------------------
    | Раздел "Каталог" - I18N
    |--------------------------------------------------------------------------
    */
    ## Функция для просмотра полной мультиязычной продукции
    public function showProduct($url){

        if (!@$url) $url = Input::get('url');

        $products = Product::with(array('meta' => function ($query) use ($url) {
            $query->where('seo_url', $url);
        }))->with('images')->get();
        $product = NULL;
        foreach ($products as $product_info):
            if (!is_null($product_info->meta->first()) && $product_info->meta->first()->seo_url == $url):
                $product = $product_info;
                break;
            endif;
        endforeach;
        if(is_null($product)):
            return App::abort(404);
        endif;
        return View::make($this->tpl.$this->template,
            array(
                'product' => $product,
                'page_title'=>$product->meta->first()->title,
                'page_description'=>'',
                'page_keywords'=>'',
                'page_author'=>'',
                'page_h1'=>$product->meta->first()->title
            )
        );
    }

}
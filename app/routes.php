<?php

$prefix = 'guest';
if(Auth::check()):
	$prefix = AuthAccount::getStartPage();
endif;

	/*
	| Общие роуты, независящие от условий
	*/

##Route::get('image/{image_group}/{id}', 'ImageController@showImage')->where('id','\d+');
Route::get('redactor/get-uploaded-images', 'DownloadsController@redactorUploadedImages');
Route::post('redactor/upload','DownloadsController@redactorUploadImage');

#Route::resource("videogid/speciality", "AdminVideogidSpecialityController", array('except' => array('show')));
	/*
	| Роуты, доступные для всех групп авторизованных пользователей
	*/

Route::group(array('before'=>'auth', 'prefix'=>$prefix), function(){

	#Route::controller('pages', 'PagesController');
	#Route::controller('galleries', 'GalleriesController');
	#Route::controller('news', 'NewsController');
	Route::controller('downloads', 'DownloadsController');
	Route::controller('articles', 'ArticlesController');

    ## I18n controllers
	#Route::controller('i18n_news', 'I18nNewsController');
	#Route::controller('i18n_news', 'AdminNewsController');
	#Route::controller('i18n_pages', 'I18nPagesController');

	##Route::delete('image/destroy/{id}', 'ImageController@deleteImage')->where('id','\d+');

    /*
    ### МОДУЛЬ КАТАЛОГА - от ВОВЫ
	Route::post('catalogs/products/upload-product-photo', 'DownloadsController@postUploadCatalogProductImages');
	Route::post('catalogs/products/upload-product-photo/product/{product_id}', 'DownloadsController@postUploadCatalogProductImages')->where('product_id','\d+');
	Route::get('catalogs/products/search-catalog-category/{category_group_id}', 'CategoriesController@getSugestSearchCategory')->where('category_group_id','\d+');
	Route::controller('catalogs/products', 'ProductsController');
    */
});

	/*
	| Роуты, доступные для группы Администраторы
	*/

#Route::group(array('before'=>'admin.auth', 'prefix'=>'admin'), function(){
Route::group(array('before'=>'auth', 'prefix'=>'admin'), function(){

    /**
     * @todo Перелопатить все админские контроллеры
     */
	Route::get('/', 'BaseController@dashboard');

	#Route::controller('users', 'UsersController');
	#Route::controller('groups', 'GroupsController');

	#Route::resource('languages', 'LangController');
	#Route::resource('templates', 'TempsController');
	#Route::resource('templates.languages', 'TempsLangController');

	#Route::controller('settings', 'SettingsController');

	##Route::controller('catalogs/categories', 'CategoriesController');
	##Route::controller('catalogs/manufacturers', 'ManufacturersController');

    /*
    ### МОДУЛЬ КАТАЛОГА - от ВОВЫ
	Route::get('catalogs/category-group/{category_group_id}/categories', 'CategoriesController@getCategoryList')->where('category_group_id','\d+');
	Route::get('catalogs/category-group/{category_group_id}/categories/create', 'CategoriesController@getCategoryCreate')->where('category_group_id','\d+');
	Route::post('catalogs/category-group/{category_group_id}/categories/store', 'CategoriesController@postCategoryStore')->where('category_group_id','\d+');
	Route::get('catalogs/category-group/{category_group_id}/categories/edit/{category_id}', 'CategoriesController@getCategoryEdit')->where('category_group_id','\d+')->where('category_id','\d+');
	Route::post('catalogs/category-group/{category_group_id}/categories/update/{category_id}', 'CategoriesController@postCategoryUpdate')->where('category_group_id','\d+')->where('category_id','\d+');
	Route::delete('catalogs/category-group/{category_group_id}/categories/destroy/{category_id}', 'CategoriesController@postCategoryDestroy')->where('category_group_id','\d+')->where('category_id','\d+');

	Route::get('catalogs/category-group/{category_group_id}/category/{parent_category_id}/sub-categories', 'CategoriesController@getCategoryList')->where('category_group_id','\d+')->where('parent_category_id','\d+');
	Route::get('catalogs/category-group/{category_group_id}/category/{parent_category_id}/sub-categories/create', 'CategoriesController@getCategoryCreate')->where('category_group_id','\d+')->where('parent_category_id','\d+');
	Route::get('catalogs/category-group/{category_group_id}/category/{parent_category_id}/sub-categories/edit/{category_id}', 'CategoriesController@getSubCategoryEdit')->where('category_group_id','\d+')->where('parent_category_id','\d+')->where('category_id','\d+');

	Route::controller('catalogs', 'CatalogsController');
    */
});


	/*
	| Роуты, доступные для группы Пользователи
	*/
/*
Route::group(array('before'=>'user.auth', 'prefix'=>'dashboard'), function(){
	Route::get('/', 'UserCabinetController@mainPage');
});
*/
	/*
	| Роуты, доступные только для неавторизованных пользователей
	*/

Route::group(array('before'=>'guest', 'prefix'=>Config::get('app.local')), function(){
	Route::post('signin', array('as'=>'signin', 'uses'=>'GlobalController@signin'));
	Route::post('signup', array('as'=>'signup', 'uses'=>'GlobalController@signup'));
	Route::get('activation', array('as'=>'activation', 'uses'=>'GlobalController@activation'));
});

	/*
	| Роуты, доступные только для авторизованных пользователей "UPK"
	*/
    /*
Route::group(array('before'=>'auth', 'prefix'=>Config::get('app.local')), function(){
	Route::get('intranet', 'UserCabinetController@getSecurePageIntranet');
});
    */

	/*
	| Роуты, доступные для гостей и авторизованных пользователей
	*/
##Route::post('request-to-access', array('as'=>'request-to-access', 'uses'=>'GlobalController@postRequestToAccess'));
Route::get('login', array('before'=>'login', 'as'=>'login', 'uses'=>'GlobalController@loginPage'));
Route::get('logout', array('before'=>'auth', 'as'=>'logout', 'uses'=>'GlobalController@logout'));
#Route::get('/news/{news_url}','HomeController@showNews'); # i18n_news enabled
Route::get('/articles/{article_url}','HomeController@showArticle');
##Route::get('catalog/{url}','HomeController@showProduct');


	/*
	| Роуты для страниц с мультиязычностью (I18N)
	*/
    ## Загружаются из модулей
/*
foreach(Config::get('app.locales') as $locale) {
	## Генерим роуты с префиксом (первый сегмент), который будет указывать на текущую локаль
	## Также указываем before-фильтр i18n_url, для выставления текущей локали
    Route::group(array('prefix' => $locale, 'before' => 'i18n_url'), function(){
        Route::get('/{url}','HomeController@showI18nPage'); ## I18n Pages
        Route::get('/', 'HomeController@showI18nPage'); ## I18n Main Page
        
        #Route::get('/news/{url}', array('as' => 'news_full', 'uses' => 'HomeController@showI18nNews')); ## I18n News
        
    });
    ## Генерим те же самые роуты, но уже без префикса, и назначаем before-фильтр i18n_url
    ## Это позволяет нам делать редирект на урл с префиксом только для этих роутов, не затрагивая, например, /admin и /login
    Route::group(array('before' => 'i18n_url'), function(){
        Route::get('/{url}','HomeController@showI18nPage'); ## I18n Pages
        Route::get('/', 'HomeController@showI18nPage'); ## I18n Main Page
        
        #Route::get('/news/{url}', array('as' => 'news_full',
        #	function($url) {
        #		return HomeController::showI18nNews($url);
        #	}
        #)); ## I18n News
        
    });
}
*/
#Route::get('/','HomeController@showPage');


## На всякий случай объявим шаблон с ошибкой 404
App::missing(function ($exception) {
    return Response::view('error404', array(), 404);
});




/***********************************************************************/
/******************** ЗАГРУЗКА РЕСУРСОВ ИЗ МОДУЛЕЙ *********************/
/***********************************************************************/
## For debug
$load_debug = 0;
## Reserved methods for return resourses of controller
$returnRoutes = "returnRoutes";
$returnActions = "returnActions";
$returnShortCodes = "returnShortCodes";
$returnExtFormElements = "returnExtFormElements";
$returnInfo = "returnInfo";
$returnMenu = "returnMenu";
## Find all controllers & load him resoures: routes, shortcodes & others...
$postfix = ".controller.php";
$mod_path = "../app/modules/*/*".$postfix;
$files = glob($mod_path);
#print_r($files); die;
## Work with each module
$mod_actions = array();
$mod_info = array();
$mod_menu = array();
$default_actions = Config::get('actions');
foreach ($files as $file) {

    #$dir_name = basename(dirname($file));

    $file_name = basename($file);
    $tmp_module_name = $module_name = str_replace($postfix, "", $file_name);

    if (strpos($module_name, ".")) {
        $blocks = explode(".", $module_name);
        foreach ($blocks as $b => $block) {
            $blocks[$b] = ucfirst($block);
        }
        $module_name = implode("", $blocks);
    }
    
    $module_prefix = "";
    $module_postname = $module_name;
    if (strpos($module_name, "_"))
        list($module_prefix, $module_postname) = explode("_", $module_name, 2);
    $module_prefix = strtolower($module_prefix);

    $module_fullname = ucfirst($module_prefix).ucfirst($module_postname)."Controller";

    if ($load_debug)
        echo $file_name . ": " . $module_prefix . " | " . $module_name . " | " . $module_fullname . " > "; #die;

    ## If class have right name...

    if (class_exists($module_fullname)) {

        ## Load routes...
        if (method_exists($module_fullname, $returnRoutes)) {
            if ($load_debug) echo " [ load routes... ] ";
            $module_fullname::$returnRoutes($module_prefix);
        }
        ## Load shortcodes...
        if (method_exists($module_fullname, $returnShortCodes)) {
            if ($load_debug) echo " [ load shortcodes... ] ";
            $module_fullname::$returnShortCodes();
        }
        ## Load Extended Form elements...
        if (method_exists($module_fullname, $returnExtFormElements)) {
            if ($load_debug) echo " [ load extended form elements... ] ";
            $module_fullname::$returnExtFormElements();
        }
        
        #if (!isset($module_fullname::$name))
        #    continue;

        ## Get module name...
        $module_name = $module_fullname::$name;

        ## Load module info...
        if (method_exists($module_fullname, $returnInfo)) {
            if ($load_debug) echo " [ load info... ] ";
            $mod_info[$module_name] = $module_fullname::$returnInfo();
        }
        
        ## Load module actions...
        $actions = array();
        if (method_exists($module_fullname, $returnActions)) {
            if ($load_debug) echo " [ load actions... ] ";
            $actions = $module_fullname::$returnActions();
        }
        #$mod_actions[$module_name] = $actions === false ? $default_actions : $actions; #array_merge($default_actions, $actions);
        $mod_actions[$module_name] = $actions;

        ## Load module admin menu elements...
        if (method_exists($module_fullname, $returnMenu)) {
            if ($load_debug) echo " [ load menus... ] ";
            $mod_menu[$module_name] = $module_fullname::$returnMenu();
        }

    } else {

        if ($load_debug) echo " CLASS NOT FOUND: {$module_fullname} | composer dump-autoload OR file name start with DIGIT!";
        
    }
    
    if ($load_debug) echo "<br/>\n";
}
#Helper::dd($mod_actions);

/*
foreach ($mod_actions as $module_name => $actions) {
    if (!count($actions))
        continue;
    $title = isset($mod_info[$module_name]['title']) ? $mod_info[$module_name]['title'] : $module_name;
    echo "<h2>{$title} - ОТКЛЮЧИТЬ МОДУЛЬ ДЛЯ ТЕКУЩЕЙ ГРУППЫ | РАЗРЕШИТЬ / ЗАПРЕТИТЬ ВСЕ ДЕЙСТВИЯ</h2>\n";
    foreach ($actions as $a => $action) {
        echo "<p>{$action} - РАЗРЕШЕНО / ЗАПРЕЩЕНО</p>";
    }
}
*/
#Helper::dd($mod_info);
#Helper::dd($mod_actions);
#Helper::dd($mod_menu);

Config::set('mod_info', $mod_info);
Config::set('mod_actions', $mod_actions);
Config::set('mod_menu', $mod_menu);
#View::share('mod_actions', $mod_actions);
#print_r($app);

/***********************************************************************/
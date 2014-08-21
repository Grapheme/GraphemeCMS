<?php

class AdminChannelController extends BaseController {

    public static $name = 'channels';
    public static $group = 'channels';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {

        $class = __CLASS__;
        Route::group(array('before' => 'auth', 'prefix' => $prefix), function() use ($class) {
        	Route::controller($class::$group."/".$class::$name, $class);
        });
    }

    ## Extended Form elements of module
    public static function returnExtFormElements() {
        #
    }

    ## Actions of module (for distribution rights of users)
    ## return false;   # for loading default actions from config
    ## return array(); # no rules will be loaded
    public static function returnActions() {
        #
    }

    ## Info about module (now only for admin dashboard & menu)
    public static function returnInfo() {
        #
    }

    /****************************************************************************/

    protected $channel;

	public function __construct(Channel $channel){

        $this->channel = $channel;
		#$this->beforeFilter('groups');

        $this->module = array(
            'name' => self::$name,
            'group' => self::$group,
            'rest' => self::$group . "/" . self::$name,
            'tpl'  => static::returnTpl('admin/' . self::$name),
            'gtpl' => static::returnTpl(),
        );
        View::share('module', $this->module);
	}

	public function getIndex(){

        $limit = 30;

        Allow::permission($this->module['group'], 'channal_view');

        $category = ChannelCategory::where('id', Input::get('cat'))->first();
        $categories = ChannelCategory::all();

        $cat = Input::get('cat');
		$channels = new Channel;
        $channels = is_numeric($cat) ? $channels->where('category_id', $cat)->paginate($limit) : $channels->paginate($limit);

		return View::make($this->module['tpl'].'index', compact('channels', 'categories', 'cat', 'category'));
	}

    /****************************************************************************/

	public function getCreate(){

        Allow::permission($this->module['group'], 'channel_create');

        $cat = Input::get('cat');

        $categories = array('Выберите категорию');
        $temp = ChannelCategory::all();
        foreach ($temp as $tmp) {
            $categories[$tmp->id] = $tmp->title;
        }
        $templates = $this->templates(__DIR__);
		return View::make($this->module['tpl'].'create', compact('categories', 'templates', 'cat'));
	}

	public function postStore(){

        if(!Request::ajax()) return App::abort(404);
        Allow::permission($this->module['group'], 'channel_create');
        $json_request = array('status'=>FALSE, 'responseText'=>'', 'responseErrorText'=>'', 'redirect'=>FALSE, 'gallery'=>0);
        $validation = Validator::make(Input::all(), Product::$rules);
        if($validation->passes()) {
            self::saveChannelModel();
            $json_request['gallery'] = self::saveChannelGallery();
            $json_request['responseText'] = "Элемент канала создан";
            $json_request['redirect'] = link::auth($this->module['rest']);
            $json_request['status'] = TRUE;
        } else {
            $json_request['responseText'] = 'Неверно заполнены поля';
            $json_request['responseErrorText'] = implode($validation->messages()->all(),'<br />');
        }
        return Response::json($json_request, 200);
	}

    /****************************************************************************/

	public function getEdit($id){

        Allow::permission($this->module['group'], 'channel_edit');
        $channel = $this->channel->where('id',$id)->with('images')->with('gallery')->first();
        $categories = array('Выберите категорию');
        foreach (ChannelCategory::all() as $category):
            $categories[$category->id] = $category->title;
        endforeach;

        $templates = $this->templates(__DIR__);
        return View::make($this->module['tpl'].'edit', compact('channel', 'templates', 'categories'));
	}

	public function postUpdate($id){

        if(!Request::ajax()) return App::abort(404);
        Allow::permission($this->module['group'], 'channel_edit');
        $json_request = array('status'=>FALSE, 'responseText'=>'', 'responseErrorText'=>'', 'redirect'=>FALSE, 'gallery'=>0);
        $validation = Validator::make(Input::all(), Product::$rules);
        if($validation->passes()):
            $channel = $this->channel->find($id);
            self::saveChannelModel($channel);
            $json_request['gallery'] = self::saveChannelGallery();
            $json_request['responseText'] = 'Элемент канала обновлен';
            $json_request['status'] = TRUE;
        else:
            $json_request['responseText'] = 'Неверно заполнены поля';
            $json_request['responseErrorText'] = implode($validation->messages()->all(), '<br />');
        endif;

        return Response::json($json_request, 200);
	}

    /****************************************************************************/

    public function deleteDestroy($id){

        if(!Request::ajax()) return App::abort(404);
        Allow::permission($this->module['group'], 'channel_delete');
        $json_request = array('status'=>FALSE, 'responseText'=>'');
        if(Request::ajax()):
            $channel = $this->channel->find($id);
            if (!empty($channel->file) && File::exists(public_path($channel->file))):
                File::delete(public_path($channel->file));
            endif;
            if($image = $channel->images()->first()):
                if (!empty($image->name) && File::exists(public_path('uploads/galleries/thumbs/'.$image->name))):
                    File::delete(public_path('uploads/galleries/thumbs/'.$image->name));
                    File::delete(public_path('uploads/galleries/'.$image->name));
                    Photo::find($image->id)->delete();
                endif;
            endif;
            $channel->delete();
            $json_request['responseText'] = 'Элемент канала удален';
            $json_request['status'] = TRUE;
        else:
            return App::abort(404);
        endif;
        return Response::json($json_request,200);
    }

    private function saveChannelModel($channel = NULL){

        if(is_null($channel)):
            $channel = $this->channel;
        endif;

        $channel->title = Input::get('title');
        $channel->link = BaseController::stringTranslite(Input::get('link'));
        $channel->category_id = Input::get('category_id');
        $channel->short = Input::get('short');
        $channel->desc = Input::get('desc');
        $channel->template = Input::get('template');
        $channel->image_id =  Input::get('image');

        if ($newFileName = $this->getUploadedFile(Input::get('file'))):
            File::delete(public_path($channel->file));
            $channel->file = $newFileName;
        endif;
        ## Сохраняем в БД
        $channel->save();
        $channel->touch();

        $this->channel = $channel;
        return TRUE;
    }

    private function saveChannelGallery(){

        $gallery_id = FALSE;
        if(Allow::action('admin_galleries','edit')):
            $gallery_id = ExtForm::process('gallery', array(
                'module'  => 'products',
                'unit_id' => $this->channel->id,
                'gallery' => Input::get('gallery'),
            ));
        endif;

        $this->channel->gallery_id = $gallery_id;
        $this->channel->save();

        return $gallery_id;
    }
}

<?php

class FeedbackController extends BaseController {

    public static $name = 'feedback';
    public static $group = 'feedback';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {
        $class = __CLASS__;
    	Route::get( "/feedback", $class."@getFeedback");
    	Route::post("/feedback", $class."@postFeedback");
    	Route::post("/feedback/send", $class."@postSendmessage");
    }

    /****************************************************************************/
    
	public function __construct() {

        $this->module = array(
            'name' => self::$name,
            'group' => self::$group,
            #'rest' => self::$group."/actions",
            #'tpl' => static::returnTpl('admin/actions'),
            'gtpl' => static::returnTpl(),
        );
        View::share('module', $this->module);
	}

    public function getFeedback() {

        #Helper::d( Input::all() );
        
        return View::make($this->module['gtpl'].'form', array());
    }
    
    public function postFeedback() {

        $name = Input::get('name');
        $email = Input::get('email');
        $message = Input::get('message');
        
        $data = array(
            'name'    => $name,
            'email'   => $email,
            'message' => $message,
        );

        $sended = $this->postSendmessage($name, $email, $message);
        
        if ($sended) {

            return View::make($this->module['gtpl'].'form', $data);
            #Redirect("/feedback?send=" . (int)$sended);
            
        } else {

            #Helper::d("ERROR");

            $data['error_messages'] = implode($validator->messages()->all(), '<br />');
            #Helper::d($data);
        	#Redirect::to('/feedback')->withErrors($validator);
            return View::make($this->module['gtpl'].'form', $data);
        }
        
    }
    
    public function postSendmessage($name = false, $email = false, $message = false) {

        $sended = false;
		$json_request = array('status'=>FALSE, 'responseText'=>'', 'responseErrorText'=>'', 'redirect'=>FALSE);

        /* !!! */
        $name = Input::get('name');
        $email = Input::get('email');
        $message = Input::get('message');

        $data = array(
            'name'    => $name,
            'email'   => $email,
            'content' => $message,
        );

        #$json_request['responseErrorText'] = print_r($data, 1);
        #return Response::json($json_request, 200);
        
        $rules = array(
            'name'    => 'required',
            'email'   => 'required|email',
            'content' => 'required',
        );
        
        $validator = Validator::make($data, $rules);

        if ($validator->passes()) {

            #Helper::d("ALL OK");
            
            // Переданные данные успешно прошли проверку.
            $sended = Mail::send($this->module['gtpl'].'feedback', $data, function ($message) use ($email, $name) {
                $message->from($email, $name);
                $message->to( Config::get('mail.for_feedback') );
            });

			#$json_request['responseText'] = 'Страница создана';
			#$json_request['redirect'] = link::auth('pages');
			$json_request['status'] = TRUE;
            
        } else {

            $json_request['responseErrorText'] = print_r($data, 1) . " | " . $validator->messages()->all();

        }

        return Response::json($json_request, 200);

    }
    
}



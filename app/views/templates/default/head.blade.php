<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>{{{(isset($page_title))?$page_title:Config::get('app.default_page_title')}}}</title>
<meta name="description" content="{{{(isset($page_description))?$page_description:''}}}">
{{ HTML::style('theme/css/normalize.css') }}
{{ HTML::style('theme/css/main.css') }}
{{ HTML::script('js/vendor/modernizr-2.6.2.min.js') }}
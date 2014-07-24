@extends('templates.'.AuthAccount::getStartPage())
@section('style')
{{ HTML::style('css/redactor.css') }}
@stop
@section('content')
	@include($module_tpl.'forms.edit')
@stop
@section('scripts')
{{ HTML::script('js/modules/news.js') }}
{{ HTML::script('js/vendor/jquery.ui.datepicker-ru.js') }}
<script src="{{ link::path('js/vendor/jquery.ui.datepicker-ru.js') }}"></script>
<script type="text/javascript">
    if(typeof pageSetUp === 'function'){pageSetUp();}
    if(typeof runFormValidation === 'function'){
        loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}",runFormValidation);
    }else{
        loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}");
    }
</script>
{{ HTML::script('js/modules/gallery.js') }}
{{ HTML::script('js/vendor/redactor.min.js') }}
{{ HTML::script('js/system/redactor-config.js') }}
@stop
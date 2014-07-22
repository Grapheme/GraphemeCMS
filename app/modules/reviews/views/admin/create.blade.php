@extends('templates.'.AuthAccount::getStartPage())


@section('style')
<link rel="stylesheet" href="{{ link::path('css/redactor.css') }}" />
@stop


@section('content')
@include($module_tpl.'forms.create')
@stop


@section('scripts')
{{ HTML::script('js/modules/reviews.js') }}
<script type="text/javascript">
    if(typeof pageSetUp === 'function'){pageSetUp();}
    if(typeof runFormValidation === 'function'){
        loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}",runFormValidation);
    }else{
        loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}");
    }
</script>
{{ HTML::script('js/modules/gallery.js') }}
<script src="{{ link::path('js/vendor/redactor.min.js') }}"></script>
<script src="{{ link::path('js/system/redactor-config.js') }}"></script>
@stop

@extends('templates.'.AuthAccount::getStartPage())


@section('style')
<link rel="stylesheet" href="{{ link::path('css/redactor.css') }}" />
@stop


@section('content')
	@include($module['tpl'].'forms.create')
@stop


@section('scripts')
    <script>
    var essence = 'tag';
    var essence_name = 'тег';
	var validation_rules = {
		module: { required: true }
	};
	var validation_messages = {
		module: { required: 'Укажите название' }
	};
    </script>

	<script src="{{ link::to('js/modules/standard.js') }}"></script>

	<script type="text/javascript">
		if(typeof pageSetUp === 'function'){pageSetUp();}
		if(typeof runFormValidation === 'function'){
			loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}", runFormValidation);
		}else{
			loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}");
		}
	</script>
	<script src="{{ link::path('js/vendor/redactor.min.js') }}"></script>
	<script src="{{ link::path('js/system/redactor-config.js') }}"></script>
@stop


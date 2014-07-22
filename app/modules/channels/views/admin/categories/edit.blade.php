@extends(Helper::acclayout())


@section('style')

@stop


@section('content')
    <h1>Информационные блоки: Изменить категорию &laquo;{{ $category->title }}&raquo;</h1>

{{ Form::model($category, array('url'=>link::auth($module['rest'].'/update/'.$category->id), 'class'=>'smart-form', 'id'=>'category-form', 'role'=>'form', 'method'=>'post')) }}
	<div class="row margin-top-10">
		<section class="col col-6">
			<div class="well">
				<header>Для изменения категории отредактируйте форму:</header>
				<fieldset>
					<section>
						<label class="label">Название</label>
						<label class="input">
							{{ Form::text('title') }}
						</label>
					</section>

				</fieldset>
				<footer>
					<a class="btn btn-default no-margin regular-10 uppercase pull-left btn-spinner" href="{{URL::previous()}}">
						<i class="fa fa-arrow-left hidden"></i> <span class="btn-response-text">Назад</span>
					</a>
					<button type="submit" autocomplete="off" class="btn btn-success no-margin regular-10 uppercase btn-form-submit">
						<i class="fa fa-spinner fa-spin hidden"></i> <span class="btn-response-text">Изменить</span>
					</button>
				</footer>
			</div>
		</section>
	</div>
{{ Form::close() }}
@stop


@section('scripts')
    <script>
    var essence = 'category';
    var essence_name = 'категория';
	var validation_rules = {
		title: { required: true },
		//desc: { required: true },
	};
	var validation_messages = {
		title: { required: 'Укажите название' },
		//desc: { required: 'Укажите описание' },
	};
    </script>

    {{ HTML::script('js/modules/standard.js') }}

	<script type="text/javascript">
		if(typeof pageSetUp === 'function'){pageSetUp();}
		if(typeof runFormValidation === 'function'){
			loadScript("{{ asset('js/vendor/jquery-form.min.js') }}", runFormValidation);
		}else{
			loadScript("{{ asset('js/vendor/jquery-form.min.js') }}");
		}
	</script>
@stop
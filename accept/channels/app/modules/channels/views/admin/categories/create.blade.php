@extends(Helper::acclayout())


@section('style')

@stop


@section('content')

    <h1>Информационные блоки: Новая категория</h1>

{{ Form::open(array('url'=>link::auth($module['rest'].'/store'), 'role'=>'form', 'class'=>'smart-form', 'id'=>'category-form', 'method'=>'post')) }}
	<div class="row margin-top-10">
		<section class="col col-6">
			<div class="well">
				<header>Для создания новой категории заполните форму:</header>
				<fieldset>
                    <section>
                        <label class="label">
                            Идентификатор страницы
                            <div class="note">Может содержать <strong>только</strong> английские буквы в нижнем регистре, цифры, знаки подчеркивания и тире</div>
                        </label>
                        <label class="input col-5"> <i class="icon-append fa fa-list-alt"></i>
                            {{ Form::text('slug','') }}
                        </label>
                    </section>
					<section>
						<label class="label">Название</label>
						<label class="input">
							{{ Form::text('title', '') }}
						</label>
					</section>
				</fieldset>
				<footer>
					<a class="btn btn-default no-margin regular-10 uppercase pull-left btn-spinner" href="{{URL::previous()}}">
						<i class="fa fa-arrow-left hidden"></i> <span class="btn-response-text">Назад</span>
					</a>
					<button type="submit" autocomplete="off" class="btn btn-success no-margin regular-10 uppercase btn-form-submit">
						<i class="fa fa-spinner fa-spin hidden"></i> <span class="btn-response-text">Создать</span>
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
        slug: { required: true },
		title: { required: true },
		//desc: { required: true },
	};
	var validation_messages = {
        slug: { required: 'Укажите идентификатор' },
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

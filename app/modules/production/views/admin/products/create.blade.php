@extends(Helper::acclayout())


@section('style')
    <link rel="stylesheet" href="{{ link::path('css/redactor.css') }}" />
@stop


@section('content')

    <h1>Продукция: Новый продукт</h1>

{{ Form::open(array('url'=>link::auth($module['rest'].'/store'), 'role'=>'form', 'class'=>'smart-form', 'id'=>'product-form', 'method'=>'post')) }}
	<div class="row margin-top-10">
		<section class="col col-6">
			<div class="well">
				<header>Для создания нового продукта заполните форму:</header>
				<fieldset>

					<section>
						<label class="label">Название</label>
						<label class="input">
							{{ Form::text('title', '') }}
						</label>
					</section>
                    <section>
						<label class="label">Ссылка на страницу</label>
						<label class="input">
							{{ Form::text('link', '') }}
						</label>
					</section>
					<section>
						<label class="label">Категория</label>
						<label class="select">
							{{ Form::select('category_id', $categories, ($cat > 0 ? $cat : '' ) ) }}
						</label>
					</section>

                    @if (Allow::module('galleries'))
                    <section>
                        <label class="label">Изображение</label>
                        <label class="input">
                            {{ ExtForm::image('image', '') }}
                        </label>
                    </section>
                    @endif

					<section>
						<label class="label">Краткое описание</label>
						<label class="textarea">
							{{ Form::textarea('short','',array('class'=>'redactor')) }}
						</label>
					</section>
                    <section>
                        <label class="label">Краткое описание</label>
                        <label class="textarea">
                            {{ Form::textarea('desc','',array('class'=>'redactor')) }}
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
    var essence = 'product';
    var essence_name = 'продукт';
	var validation_rules = {
		title: { required: true },
		category_id: { required: true, min: 1 },
		link: { required: true},
		//desc: { required: true },
		//image: { required: true },
	};
	var validation_messages = {
		title: { required: 'Укажите название' },
		category_id: { required: 'Укажите категорию', min: 'Укажите категорию' },
        link : { required: 'Укажите ссылку на товар' }
		//desc: { required: 'Укажите описание' },
		//image: { required: 'Загрузите изображение' },
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

    {{ HTML::script('js/modules/gallery.js') }}
    {{ HTML::script('js/vendor/redactor.min.js') }}
    {{ HTML::script('js/system/redactor-config.js') }}

@stop
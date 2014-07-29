@extends(Helper::acclayout())
@section('style')
    <link rel="stylesheet" href="{{ link::path('css/redactor.css') }}" />
@stop
@section('content')
    <h1>Продукция: &laquo;{{ $product->meta->first()->title }}&raquo;</h1>
{{ Form::model($product, array('url'=>link::auth($module['rest'].'/update/'.$product->id), 'class'=>'smart-form', 'id'=>'product-form', 'role'=>'form', 'method'=>'post')) }}

	<div class="well">
        <header>Для изменения продукта отредактируйте форму:</header>
        <fieldset>
            <section>
                <label class="label">Категория</label>
                <label class="select">
                    {{ Form::select('category_id', $categories) }}
                </label>
            </section>
        </fieldset>
    </div>
    <ul class="nav nav-tabs margin-top-10">
        @foreach ($locales as $l => $locale)
        <li class="{{ $l === 0 ? 'active' : '' }}">
            <a href="#lang_{{ $locale }}" data-toggle="tab">{{ $locale }}</a>
        </li>
        @endforeach
    </ul>
    <div class="row margin-top-10">
        <div class="tab-content">
            @foreach ($locales as $l => $locale)
            <div class="tab-pane{{ $l === 0 ? ' active' : '' }}" id="lang_{{ $locale }}">
                <!-- Form -->
                <section class="col col-6">
                    <div class="well">
                        <header>{{ $locale }}-версия:</header>
                        <fieldset>
                            <section>
                                <label class="label">Название</label>
                                <label class="input"> <i class="icon-append fa fa-list-alt"></i>
                                    {{ Form::text('title['.$locale.']',$product->meta->first()->title) }}
                                </label>
                            </section>
                            <section>
                                <label class="label">Короткое название</label>
                                <label class="input"> <i class="icon-append fa fa-list-alt"></i>
                                    {{ Form::text('short_title['.$locale.']',$product->meta->first()->short_title) }}
                                </label>
                            </section>
                            <section>
                                <label class="label">Цена</label>
                                <label class="input"> <i class="icon-append fa fa-money"></i>
                                    {{ Form::text('price['.$locale.']',$product->meta->first()->price) }}
                                </label>
                            </section>
                            @if (Allow::module('galleries'))
                            <section>
                                <label class="label">Изображение</label>
                                <label class="input">
                                    {{ ExtForm::image('image', $product->images) }}
                                </label>
                            </section>
                            <section>
                                <label class="label">Галерея</label>
                                <label class="input">
                                    {{ ExtForm::gallery('gallery',$product->gallery->first()) }}
                                </label>
                            </section>
                            @endif
                            <section>
                                <label class="label">Краткое описание</label>
                                <label class="textarea">
                                    {{ Form::textarea('preview['.$locale.']',$product->meta->first()->preview,array('class'=>'redactor redactor_150')) }}
                                </label>
                            </section>
                            <section>
                                <label class="label">Описание</label>
                                <label class="textarea">
                                    {{ Form::textarea('content['.$locale.']',$product->meta->first()->content,array('class'=>'redactor redactor_450')) }}
                                </label>
                            </section>
                        </fieldset>
                    </div>
                </section>
                @if(Allow::enabled_module('seo'))
                <section class="col col-6">
                    <div class="well">
                        @include('modules.seo.events')
                    </div>
                </section>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    <div style="float:none; clear:both;"></div>
    @if(Allow::enabled_module('galleries') && 0)
    <section class="col-12">
        @include('modules.galleries.abstract')
        @include('modules.galleries.uploaded', array('gallery' => $gall))
    </section>
    @endif
	<section class="col-6">
     <footer>
         <a class="btn btn-default no-margin regular-10 uppercase pull-left btn-spinner" href="{{URL::previous()}}">
             <i class="fa fa-arrow-left hidden"></i> <span class="btn-response-text">Назад</span>
         </a>
         <button type="submit" autocomplete="off" class="btn btn-success no-margin regular-10 uppercase btn-form-submit">
             <i class="fa fa-spinner fa-spin hidden"></i> <span class="btn-response-text">Изменить</span>
         </button>
     </footer>
    </section>
{{ Form::close() }}
@stop


@section('scripts')
    <script>
    var essence = 'product';
    var essence_name = 'продукт';
	var validation_rules = {
		category_id: { required: true, min: 1 },
	};
	var validation_messages = {
		category_id: { required: 'Укажите категорию', min: 'Укажите категорию' },
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
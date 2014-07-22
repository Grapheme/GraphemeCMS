@extends('templates.'.AuthAccount::getStartPage())


@section('content')

    <h1>Страницы</h1>

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="margin-bottom-25 margin-top-10 ">
			@if(Allow::action('pages','create'))
				<a class="btn btn-primary" href="{{ link::auth('pages/create') }}">Добавить страницу</a>
			@endif
			@if(Allow::action('pages','sort') && $pages->count() > 2)
				<!-- <a class="btn btn-default" href="{{ link::auth('pages/menu') }}">Сортировать меню</a> -->
			@endif
			</div>
		</div>
	</div>

	@if($pages->count())
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<table class="table table-striped table-bordered min-table">
				<thead>
					<tr>
						<th class="col col-sm-3 text-center">Название страницы</th>
						<th class="col col-sm-3 text-center">URL</th>
					    @if(Allow::action('pages','publication'))
						<!--<th class="text-center">Публикация</th>-->
					    @endif
						<th class="col col-sm-4 text-center">Действия</th>
					</tr>
				</thead>
				<tbody>
				@foreach($pages as $page)
					<tr>
						<td>{{ $page->metas->first()->name }}</td>
						<td class="wigth-250 text-center">
						@if($page->start_page == 1)
							<a href="{{ link::to($page->seo_url) }}" target="_blank">На главную</a>
						@else
							<a href="{{ link::to($page->slug) }}" target="_blank">{{ $page->slug }}</a>
						@endif
						</td>
						@if(Allow::action('pages','publication') && 0)
						<td class="wigth-100">
							<div class="smart-form">
								<label class="toggle pull-left">
									<input type="checkbox" name="publication" checked="{{ $page->publication ? 'checked' : '' }}" value="{{ $page->publication }}">
									<i data-swchon-text="да" data-swchoff-text="нет"></i>
								</label>
							</div>
						</td>
						@endif
						<td class="wigth-250" nowrap="nowrap">
						@if(Allow::action('pages','edit'))
							<a class="btn btn-default pull-left margin-right-10" href="{{ link::auth('pages/edit/'.$page->id) }}">
								Редактировать
							</a>
						@endif
						@if(Allow::action('pages','delete'))
							<form method="POST" action="{{ link::auth('pages/destroy/'.$page->id) }}">
								<button type="button" class="btn btn-default remove-page">
									Удалить
								</button>
							</form>
						@endif
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>
	@else
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="ajax-notifications custom">
				<div class="alert alert-transparent">
					<h4>Список пуст</h4>
					В данном разделе находятся страницы сайта
					<p><br><i class="regular-color-light fa fa-th-list fa-3x"></i></p>
				</div>
			</div>
		</div>
	</div>
	@endif
@stop


@section('scripts')
	<script src="{{ link::to('js/modules/pages.js') }}"></script>
	<script type="text/javascript">
		if(typeof pageSetUp === 'function'){pageSetUp();}
		if(typeof runFormValidation === 'function'){
			loadScript("{{asset('js/vendor/jquery-form.min.js');}}",runFormValidation);
		}else{
			loadScript("{{asset('js/vendor/jquery-form.min.js');}}");
		}
	</script>
@stop

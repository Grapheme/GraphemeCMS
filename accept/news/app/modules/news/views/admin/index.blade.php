@extends('templates.'.AuthAccount::getStartPage())
@section('content')
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="margin-bottom-25 margin-top-10 ">
			@if(Allow::action('news','create'))
				<a class="btn btn-primary" href="{{ link::auth('news/create') }}">Добавить новость</a>
			@endif
			@if(Allow::action('news','sort') && $news->count() > 2)
				<!-- <a class="btn btn-default" href="{{ link::auth('news/sort') }}">Сортировать</a> -->
			@endif
			</div>
		</div>
	</div>
	@if($news->count())
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<table class="table table-striped table-bordered min-table">
				<thead>
					<tr>
						<th class="text-center" style="width:100px">Дата</th>
						<th class="text-center">Название новости</th>
    					@if(Allow::action('news','publication'))
						<th class="text-center">Публикация</th>
	    				@endif
	    				@if(NewsController::$prefix_url !== FALSE)
                        <th class="col col-sm-3 text-center">URL</th>
                        @endif
						<th></th>
					</tr>
				</thead>
				<tbody>
				@foreach($news as $new)
					<tr>
						<td class="text-center">{{ date("d.m.Y", strtotime($new->published_at)) }}</a></td>
						 <td>{{$new->meta->first()->title}}</td>
						@if(Allow::action('news','publication'))
						<td class="width-100">
							<div class="smart-form">
								<label class="toggle pull-left">
									<input type="checkbox" name="publication" disabled="" checked="" value="1">
									<i data-swchon-text="да" data-swchoff-text="нет"></i>
								</label>
							</div>
						</td>
						@endif
						 @if(NewsController::$prefix_url !== FALSE)
                        <td class="width-350 text-center">
                            <a href="{{ link::to(NewsController::$prefix_url.'/'.$new->meta->first()->seo_url) }}" target="_blank">{{ $new->meta->first()->seo_url }}</a>
                        </td>
                        @endif
						<td class="width-350">
						@if(Allow::action('news', 'edit'))
							<a class="btn btn-default pull-left margin-right-10" href="{{ link::auth('news/edit/'.$new->id) }}">
								Редактировать
							</a>
						@endif
						@if(Allow::action('news', 'delete'))
							<form method="POST" action="{{ link::auth('news/destroy/'.$new->id) }}">
								<button type="button" class="btn btn-default remove-news">
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
					В данном разделе находятся новости сайта
					<p><br><i class="regular-color-light fa fa-th-list fa-3x"></i></p>
				</div>
			</div>
		</div>
	</div>
	@endif
@stop


@section('scripts')
	<script src="{{ url('js/modules/news.js') }}"></script>
	<script type="text/javascript">
		if(typeof pageSetUp === 'function'){pageSetUp();}
		if(typeof runFormValidation === 'function'){
			loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}", runFormValidation);
		}else{
			loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}");
		}
	</script>
@stop


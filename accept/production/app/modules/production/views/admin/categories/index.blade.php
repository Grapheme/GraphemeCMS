@extends(Helper::acclayout())


@section('content')
    <h1>Продукция: Категории продуктов</h1>

    <div class="row">
    	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-bottom-25 margin-top-10">
    		<div class="">
    		@if(Allow::action('production', 'category_create'))
    			<a class="btn btn-primary" href="{{ link::auth($module['rest'].'/create')}}">Новая категория</a>
    		@endif
    		</div>
    	</div>
    </div>

    @if($categories->count())
    <div class="row">
    	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
    		<table class="table table-striped table-bordered">
    			<thead>
    				<tr>
                        {{--
    					<th class="col-lg-1 text-center">ID</th>
                        --}}
    					<th class="col-lg-10 text-center" style="white-space:nowrap;">Категория</th>
    					<th class="col-lg-1 text-center">Действия</th>
    				</tr>
    			</thead>
    			<tbody>
    			@foreach($categories as $category)
    				<tr class="vertical-middle">
                        {{--
    					<td class="text-center">{{ $category->id }}</td>
                        --}}
    					<td>
                            {{ $category->title }}
                        </td>
    					<td class="text-center" style="white-space:nowrap;">

    						@if(Allow::action($module['group'], 'product_view'))
    							<a class="btn btn-info margin-right-10" style="display:inline-block" href="{{ link::auth('production/products?cat='.$category->id) }}">
    								Продукты ({{ $category->count_products() }})
    							</a>
    						@endif

        					@if(Allow::action($module['group'], 'category_edit'))
							<form method="GET" action="{{ link::auth($module['rest'].'/edit/'.$category->id) }}" style="display:inline-block">
								<button type="submit" class="btn btn-success margin-right-10">
									Изменить
								</button>
							</form>
                    		@endif

        					@if(Allow::action($module['group'], 'category_delete'))
							<form method="POST" action="{{ link::auth($module['rest'].'/destroy/'.$category->id) }}" style="display:inline-block">
								<button type="submit" class="btn btn-danger remove-category">
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
    				В данном разделе находятся категории продукции
    				<p><br><i class="regular-color-light fa fa-th-list fa-3x"></i></p>
    			</div>
    		</div>
    	</div>
    </div>
@endif

@stop


@section('scripts')
    <script>
    var essence = 'category';
    var essence_name = 'категорию';
	var validation_rules = {
		title: { required: true },
		desc: { required: true },
	};
	var validation_messages = {
		title: { required: 'Укажите название' },
		desc: { required: 'Укажите описание' },
	};
    </script>
	<script src="{{ url('js/modules/standard.js') }}"></script>


	<script type="text/javascript">
		if(typeof pageSetUp === 'function'){pageSetUp();}
		if(typeof runFormValidation === 'function'){
			loadScript("{{ asset('js/vendor/jquery-form.min.js') }}", runFormValidation);
		}else{
			loadScript("{{ asset('js/vendor/jquery-form.min.js') }}");
		}
	</script>
@stop

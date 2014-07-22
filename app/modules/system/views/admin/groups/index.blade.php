@extends('templates.'.AuthAccount::getStartPage())


@section('content')
    <h1>Группы пользователей</h1>

    <div class="row">
    	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-bottom-25 margin-top-10">
    		<div class="">
    		@if(Allow::action('groups', 'create'))
    			<a class="btn btn-primary" href="{{ link::auth($module['rest'].'/create')}}">Добавить группу</a>
    		@endif
    		</div>
    	</div>
    </div>

    @if($groups->count())
    <div class="row">
    	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
    		<table class="table table-striped table-bordered">
    			<thead>
    				<tr>
    					<th class="col-lg-1 text-center">ID</th>
    					<th class="col-lg-10 text-center" style="white-space:nowrap;">Название группы</th>
    					<th class="col-lg-1 text-center">Действия</th>
    				</tr>
    			</thead>
    			<tbody>
    			@foreach($groups as $group)
    				<tr class="vertical-middle">
    					<td class="text-center">{{ $group->id }}</td>
    					<td>
                            {{ $group->desc }}
                            <div style="margin:0; padding:0; font-size:80%; color:#777">Пользователей: {{ $group->count_users() }}</div>
                        </td>
    					<td class="text-center" style="white-space:nowrap;">

        					@if(Allow::action('groups', 'view'))
    						<a class="btn btn-info margin-right-10" href="{{ link::auth('users?group=' . $group->name) }}">
    							Участники
    						</a>
                    		@endif

        					@if(Allow::action('groups', 'edit'))
							<form method="GET" action="{{ link::auth($module['rest'].'/edit/'.$group->id) }}" style="display:inline-block">
								<button type="submit" class="btn btn-success margin-right-10"<? if($group->id == 1){ echo " disabled='disabled'"; }?>>
									Изменить
								</button>
							</form>
                    		@endif

        					@if(Allow::action('groups', 'delete'))
							<form method="POST" action="{{ link::auth($module['rest'].'/destroy/'.$group->id) }}" style="display:inline-block">
								<button type="submit" class="btn btn-danger remove-group"<? if($group->id == 1){ echo " disabled='disabled'"; }?>>
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
    				В данном разделе находятся группы пользователей
    				<p><br><i class="regular-color-light fa fa-th-list fa-3x"></i></p>
    			</div>
    		</div>
    	</div>
    </div>
@endif

@stop


@section('scripts')
	<script src="{{ link::to('js/modules/groups.js') }}"></script>
@stop

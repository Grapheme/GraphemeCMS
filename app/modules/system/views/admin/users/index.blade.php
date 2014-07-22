@extends('templates.'.AuthAccount::getStartPage())


@section('content')
    <h1>Пользователи</h1>

    <div class="row">
    	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-bottom-25 margin-top-10">

    		<div class="pull-left margin-right-10">
    		@if(Allow::action('users', 'create'))
    			<a class="btn btn-primary" href="{{ link::auth($module['rest'].'/create')}}">Добавить пользователя</a>
    		@endif
    		</div>

            @if($groups->count())
            <div class="btn-group pull-left margin-right-10">
                @if (is_object($group) && $group->id)
                <a class="btn btn-default" href="javascript:void(0);">
                    {{ $group->desc }} ({{ $group->count_users() }})
                </a>
                @else
                <a class="btn btn-default" href="javascript:void(0);">
                    Все пользователи ({{ User::count() }})
                </a>
                @endif
                <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    @if (is_object($group) && $group->id || 1)
                    <li>
                        <a href="?">
                            Все пользователи ({{ User::count() }})
                        </a>
                    </li>
                    <li class="divider"></li>
                    @endif
                    @foreach($groups as $group)
                    <li>
                        <a href="?group={{ $group->name }}">{{ $group->desc }} ({{ $group->count_users() }})</a>
                    </li>
                    @endforeach
                </ul>
            </div>
    		@endif

    	</div>
    </div>

    @if($users->count())
    <div class="row">
    	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
    		<table class="table table-striped table-bordered">
    			<thead>
    				<tr>
    					<th class="col-lg-1 text-center">ID</th>
    					<th class="col-lg-1 text-center">Аватар</th>
    					<th class="col-lg-9 text-center" style="white-space:nowrap;">Данные пользователя</th>
    					<th class="col-lg-1 text-center">Действия</th>
    				</tr>
    			</thead>
    			<tbody>
    			@foreach($users as $user)
    				<tr class="vertical-middle<? if($user->active == 0){ echo ' warning'; } ?>">
    					<td class="text-center">{{ $user->id }}</td>
    					<td class="text-center">
    					@if(!empty($user->thumbnail))
    						<figure class="avatar-container">
    							<img src="{{ url($user->thumbnail) }}" alt="{{ $user->name }} {{ $user->surname }}" class="avatar bordered circle">
    						</figure>
                        @else
                            <i class="fa fa-user" style="font-size:36px; color:#999"></i>
    					@endif
    					</td>
    					<td>
    						{{ $user->name }} {{ $user->surname }}
                            @if ($user->email)
                            <br/>
    						<i class="fa fa-envelope-o"></i> {{ HTML::mailto($user->email, $user->email) }}
                            @endif
    					</td>
    					<td class="text-center" style="white-space:nowrap;">

        					@if(Allow::action('users', 'edit'))
    						<a class="btn btn-success margin-right-10" href="{{ link::auth('users/edit/'.$user->id) }}">
    							Изменить
    						</a>
        					@endif

        					@if(Allow::action('users', 'delete'))
    						<form method="POST" action="{{ link::auth($module['rest'].'/destroy/'.$user->id) }}" style="display:inline-block">
    							<button type="submit" class="btn btn-danger remove-user"<? if($user->id == 1){ echo " disabled='disabled'"; }?>>
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
    				В данном разделе находятся пользователи сайта
    				<p><br><i class="regular-color-light fa fa-th-list fa-3x"></i></p>
    			</div>
    		</div>
    	</div>
    </div>
    @endif
@stop


@section('scripts')
	<script src="{{ link::to('js/modules/users.js') }}"></script>
@stop

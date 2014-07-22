@extends('templates.'.AuthAccount::getStartPage())


@section('style')

@stop


@section('content')
    <h1>Изменить группу &laquo;{{ $group->desc }}&raquo;</h1>

    <div class="row">
    	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-bottom-25 margin-top-10">

            @if($groups->count())
            <div class="btn-group pull-left margin-right-10">
                @if (is_object($group) && $group->id)
                <a class="btn btn-default" href="javascript:void(0);">
                    {{ $group->desc }} ({{ $group->count_users() }})
                </a>
                @endif
                <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    @foreach($groups as $grp)
                        @if ($grp->id != $group->id && $grp->id != 1)
                        <li>
                            <a href="{{ link::auth("groups/edit/".$grp->id) }}">{{ $grp->desc }} ({{ $grp->count_users() }})</a>
                        </li>
                        @endif
                    @endforeach
                </ul>
            </div>
    		@endif

			@if(Allow::action('users', 'view'))
			<a class="btn btn-info pull-left margin-right-10" href="{{ link::auth('users?group=' . $group->name) }}">
				Участники
			</a>
    		@endif

    	</div>
    </div>

{{ Form::model($group, array('url'=>link::auth($module['rest'].'/update/'.$group->id), 'class'=>'smart-form', 'id'=>'group-form', 'role'=>'form', 'method'=>'post')) }}
	<div class="row">
		<section class="col col-6">
			<div class="well">
				<header>Доступ группы к модулям:</header>

				<fieldset>
				@if(@count($mod_actions))
					<section>
						<div class="">
    					@foreach($mod_actions as $module_name => $actions)
                        <? #Helper::dd($actions); ?>
                            <? if (!count($actions)) continue; ?>
                            <? $title = isset($mod_info[$module_name]['title']) ? $mod_info[$module_name]['title'] : $module_name; ?>
    						<div class="row margin-bottom-10">
                                <div class="col col-8 margin-bottom-10">
                                    <h3>{{ $title }}</h3>
                                </div>

                                <div class="col col-4">
{{--
                                    <!-- Задумка на будущее: возможность отключать модуль для конкретной группы -->
                                    <!-- 1) Закрыть доступ к действиям модуля 2) Не отображать ссылки модуля в меню
                                    <input type="checkbox"{{ $checked }} class="module-checkbox" data-action="{{ $action }}">
    								<i data-swchon-text="вкл" data-swchoff-text="выкл"></i> 
--}}
                                </div>

            					@foreach($actions as $a => $action)
        							<?php $checked = ''; ?>
        							@if(@$group_actions[$module_name][$a] == 1)
        								<?php $checked = ' checked="checked"'; ?>
        							@endif 

                                    <div class="col col-8">
                                        <label for="act_{{ $module_name }}_{{ $a }}">{{ @$action }}</label>
                                    </div>
                                    <div class="col col-4">
            							<input type="checkbox"{{ $checked }} value="{{ $a }}" name="actions[{{ $module_name }}][]" class="module-checkbox" id="act_{{ $module_name }}_{{ $a }}">
        								<i data-swchon-text="вкл" data-swchoff-text="выкл"></i> 
                                    </div>
            					@endforeach

    						</div>
    					@endforeach
                        </div>
					</section>
				@endif
				</fieldset>

				<footer>
					<a class="btn btn-default no-margin regular-10 uppercase pull-left btn-spinner" href="{{URL::previous()}}">
						<i class="fa fa-arrow-left hidden"></i> <span class="btn-response-text">Назад</span>
					</a>
					<button type="submit" autocomplete="off" class="btn btn-success no-margin regular-10 uppercase btn-form-submit">
						<i class="fa fa-spinner fa-spin hidden"></i> <span class="btn-response-text">Сохранить</span>
					</button>
				</footer>

			</div>
		</section>

		<section class="col col-6">
			<div class="well">
				<header>Данные о группе:</header>
				<fieldset>

					<section>
						<label class="label">Название</label>
						<label class="input">
							{{ Form::text('name', NULL) }}
						</label>
						<div class="note">Только латинские символы, без пробелов. Например: admin</div>
					</section>

					<section>
						<label class="label">Описание</label>
						<label class="input">
							{{ Form::text('desc', NULL) }}
						</label>
						<div class="note">Описание группы. Например: Администраторы</div>
					</section>

					<section>
						<label class="label">Базовый шаблон</label>
						<label class="input">
							{{ Form::text('dashboard', NULL) }}
						</label>
						<div class="note">Наименование используемого базового шаблона. Например: default</div>
					</section>

					<section>
						<label class="label">Стартовая страница</label>
						<label class="input">
							{{ Form::text('start_url', NULL) }}
						</label>
						<div class="note">Страница, на которую попадет пользователь после авторизации. Можно оставить пустым.</div>
					</section>

				</fieldset>

				<footer>
					<a class="btn btn-default no-margin regular-10 uppercase pull-left btn-spinner" href="{{URL::previous()}}">
						<i class="fa fa-arrow-left hidden"></i> <span class="btn-response-text">Назад</span>
					</a>
					<button type="submit" autocomplete="off" class="btn btn-success no-margin regular-10 uppercase btn-form-submit">
						<i class="fa fa-spinner fa-spin hidden"></i> <span class="btn-response-text">Сохранить</span>
					</button>
				</footer>

			</div>
		</section>

	</div>
{{ Form::close() }}
@stop


@section('scripts')
	<script src="{{ link::to('js/modules/groups.js') }}"></script>
	<script type="text/javascript">
		if(typeof pageSetUp === 'function'){pageSetUp();}
		if(typeof runFormValidation === 'function'){
			loadScript("{{ asset('js/vendor/jquery-form.min.js') }}", runFormValidation);
		}else{
			loadScript("{{ asset('js/vendor/jquery-form.min.js') }}");
		}
	</script>
@stop
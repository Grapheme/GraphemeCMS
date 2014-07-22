@extends('templates.'.AuthAccount::getStartPage())


@section('content')
<div class="row">

	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-5">
		<form action="{{ link::to(AuthAccount::getStartPage().'/settings/module') }}" class="smart-form">
			<fieldset>
				<label class="label">Список доступных модулей:</label>
				<table class="table table-bordered table-striped">
					@foreach(SystemModules::getModules() as $name => $module)
					<tr>
						<td>{{ @$module['title'] }}</td>
						<td style="width: 50px;">
							<label class="toggle">
    							<?php $checked = ''; ?>
    							@if(Module::where('name', @$module['name'])->exists() && Module::where('name', @$module['name'])->first()->on == 1)
    								<?php $checked = ' checked="checked"'; ?>
    							@endif 
    							<input type="checkbox"{{ $checked }} class="module-checkbox" data-name="{{ @$module['name'] }}">
								<i data-swchon-text="вкл" data-swchoff-text="выкл"></i> 
							</label>
						</td>
					</tr>
					@endforeach
				</table>
			</fieldset>
		</form>
	</div>

</div>
@stop


@section('scripts')
	{{HTML::script('js/modules/settings.js')}}
@stop


@extends('templates.' . Config::get('app.template'))


@section('content')
    <h1>Обратная связь</h1>

<?
#Helper::d(Input::all());
?>

    @if(Input::get('send') != '' && Input::get('send') == 1)
        <div class="success">Ваше сообщение успешно отправлено.</div>
    @elseif(Input::get('send') != '' && Input::get('send') == 0)
        <div class="error">Возникла ошибка при отправке сообщения. Попробуйте еще раз.</div>
    @endif

    {{ Form::open(array('url'=>link::to('feedback'), 'role'=>'form', 'class'=>'smart-form', 'id'=>'group-form', 'method'=>'post')) }}
	<div class="row margin-top-10">
		<section class="col col-6">
			<div class="well">
				<header>Нам очень важно Ваше мнение!</header>
				<fieldset>
					<section>
						<label class="label">Представьтесь</label><br/>
						<label class="input">
							{{ Form::text('name', @$name) }}
						</label>
					</section>
					<section>
						<label class="label">Ваш e-mail</label><br/>
						<label class="input">
							{{ Form::text('email', @$email) }}
						</label>
					</section>
					<section>
						<label class="label">Сообщение</label><br/>
						<label class="input">
							{{ Form::textarea('message', @$message) }}
						</label>
					</section>
				</fieldset>
				<footer>
					<button type="submit" autocomplete="off" class="btn btn-success no-margin regular-10 uppercase btn-form-submit">
						<i class="fa fa-spinner fa-spin hidden"></i> <span class="btn-response-text">Отправить</span>
					</button>
				</footer>
			</div>
		</section>
	</div>
    {{ Form::close() }}

@stop


@section('scripts')

@stop


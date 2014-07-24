@extends('templates.'.AuthAccount::getStartPage())

@section('content')
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="margin-bottom-25 margin-top-10 ">
            @if(Allow::action('events','create'))
            <a class="btn btn-primary" href="{{ link::auth('events/create') }}">Добавить событие</a>
            @endif
        </div>
    </div>
</div>
@if($events->count())
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <table class="table table-striped table-bordered min-table">
            <thead>
            <tr>
                <th class="text-center" style="width:100px">Дата</th>
                <th class="text-center">Название</th>
                @if(Allow::action('events','publication'))
                <th class="text-center">Публикация</th>
                @endif
                 @if(EventsController::$prefix_url !== FALSE)
                <th class="col col-sm-3 text-center">URL</th>
                @endif
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($events as $event)
            <tr>
                <td class="text-center">{{ date("d.m.Y", strtotime($event->published_at)) }}</a></td>
                <td>{{ $event->meta->first()->title }}</td>
                @if(Allow::action('event','publication'))
                <td class="width-100">
                    <div class="smart-form">
                        <label class="toggle pull-left">
                            <input type="checkbox" name="publication" disabled="" checked="" value="1">
                            <i data-swchon-text="да" data-swchoff-text="нет"></i>
                        </label>
                    </div>
                </td>
                @endif
                @if(EventsController::$prefix_url !== FALSE)
                <td class="width-350 text-center">
                    <a href="{{ link::to(EventsController::$prefix_url.'/'.$event->meta->first()->seo_url) }}" target="_blank">{{ $event->meta->first()->seo_url }}</a>
                </td>
                @endif
                <td class="width-350">
                    @if(Allow::action('event', 'edit'))
                    <a class="btn btn-default pull-left margin-right-10" href="{{ link::auth('events/edit/'.$event->id) }}">
                        Редактировать
                    </a>
                    @endif
                    @if(Allow::action('news', 'delete'))
                    <form method="POST" action="{{ link::auth('events/destroy/'.$event->id) }}">
                        <button type="button" class="btn btn-default remove-event">
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
                В данном разделе находятся события
                <p><br><i class="regular-color-light fa fa-th-list fa-3x"></i></p>
            </div>
        </div>
    </div>
</div>
@endif
@stop

@section('scripts')
{{ HTML::script('js/modules/events.js') }}
{{ HTML::script('js/vendor/jquery.ui.datepicker-ru.js') }}
<script type="text/javascript">
    if(typeof pageSetUp === 'function'){pageSetUp();}
    if(typeof runFormValidation === 'function'){
        loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}", runFormValidation);
    }else{
        loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}");
    }
</script>
@stop


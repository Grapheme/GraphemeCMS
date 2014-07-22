@extends('templates.'.AuthAccount::getStartPage())

@section('content')
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="margin-bottom-25 margin-top-10 ">
            @if(Allow::action('news','create'))
            <a class="btn btn-primary" href="{{ link::auth('reviews/create') }}">Добавить отзыв</a>
            @endif
        </div>
    </div>
</div>
@if($reviews->count())
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <table class="table table-striped table-bordered min-table">
            <thead>
            <tr>
                <th class="text-center" style="width:100px">Дата</th>
                <th class="text-center">Отправитель</th>
                @if(Allow::action('reviews','publication'))
                <th class="text-center">Публикация</th>
                @endif
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($reviews as $review)
            <tr>
                <td class="text-center">{{ date("d.m.Y", strtotime($review->published_at)) }}</a></td>
                <td>{{$review->name}}</td>
                @if(Allow::action('review','publication'))
                <td class="wigth-100">
                    <div class="smart-form">
                        <label class="toggle pull-left">
                            <input type="checkbox" name="publication" disabled="" checked="" value="1">
                            <i data-swchon-text="да" data-swchoff-text="нет"></i>
                        </label>
                    </div>
                </td>
                @endif
                <td class="wigth-250">
                    @if(Allow::action('news', 'edit'))
                    <a class="btn btn-default pull-left margin-right-10" href="{{ link::auth('reviews/edit/'.$review->id) }}">
                        Редактировать
                    </a>
                    @endif
                    @if(Allow::action('news', 'delete'))
                    <form method="POST" action="{{ link::auth('reviews/destroy/'.$review->id) }}">
                        <button type="button" class="btn btn-default remove-reviews">
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
                В данном разделе находятся отзывы о сайте
                <p><br><i class="regular-color-light fa fa-th-list fa-3x"></i></p>
            </div>
        </div>
    </div>
</div>
@endif
@stop


@section('scripts')
<script src="{{ url('js/modules/reviews.js') }}"></script>
<script src="{{ link::path('js/vendor/jquery.ui.datepicker-ru.js') }}"></script>
<script type="text/javascript">
    if(typeof pageSetUp === 'function'){pageSetUp();}
    if(typeof runFormValidation === 'function'){
        loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}", runFormValidation);
    }else{
        loadScript("{{ asset('js/vendor/jquery-form.min.js'); }}");
    }
</script>
@stop


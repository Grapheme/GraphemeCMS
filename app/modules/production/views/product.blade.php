@extends(Helper::layout())

@section('style')
@stop

@section('content')
 <section class="auto-slider">
    <div class="slider-window">
        <div class="wrapper">
            <div class="colorWrapper">
                <div class="color-head">Подбор цвета</div>
                <div class="color-close">✕</div>
                <div class="color-name"></div>
                <ul class="colors-list">
                    <li class="color-item" data-color="1"></li>
                    <li class="color-item" data-color="2"></li>
                    <li class="color-item" data-color="3"></li>
                    <li class="color-item" data-color="4"></li>
                    <li class="color-item" data-color="5"></li>
                    <li class="color-item" data-color="6"></li>
                    <li class="color-item" data-color="7"></li>
                    <li class="color-item" data-color="8"></li>
                </ul>
            </div>
            <a class="drive-btn colorView" href="javascript:void(0);"><span class="icon icon-circle"></span> Выбор цвета</a>
            @if(File::exists(public_path('uploads/galleries/'.$product->images['name'])))
             <div class="slider-photo" style="background-image: url({{ asset('uploads/galleries/'.$product->images['name']) }});"></div>
            @endif
            <div class="auto-info">
                <div class="title">Объект желания</div>
                <div class="text">{{ $product->meta->first()->title }}</div>
                <div class="price">{{ number_format($product->meta->first()->price,0,' ',' ') }} руб</div>
                <div class="text">{{ $product->meta->first()->preview }}</div>
                <a href="javascript:void(0);" class="drive-btn js-pop-show" data-popup="test-drive">
                    <span class="icon icon-wheel"></span> Записаться на тестдрайв
                </a>
            </div>
        </div>
    </div>
    <div class="js-slider-nav">
        <i data-thumb="img/thumbs/thumb1.jpg" data-img="img/slide1.jpg"></i>
        <i data-thumb="img/thumbs/thumb2.jpg" data-img="img/fx35.jpg"></i>
        <i data-thumb="img/thumbs/thumb3.jpg" data-img="img/slide1.jpg"></i>
        <i data-thumb="img/thumbs/thumb4.jpg" data-img="img/slide1.jpg"></i>
        <i data-thumb="img/thumbs/thumb5.jpg" data-img="img/slide1.jpg"></i>
        <i data-thumb="img/thumbs/thumb1.jpg" data-img="img/slide1.jpg"></i>
    </div>
    <div class="slider-nav-win">
        <ul class="slider-nav"></ul>
    </div>
</section>
<section class="model-sect">
    {{ $product->meta->first()->content }}
</section>
@stop
@section('scripts')
@stop
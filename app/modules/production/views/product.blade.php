@extends(Helper::layout())

@section('style')
@stop

@section('content')
<div class="wrapper">
    <div class="us-block">
        <ul class="products">
            <li>
                <div class="product-photo"
                     style="background-image: url({{ asset('uploads/galleries/'.$product['images']['name']) }});"></div>
                <div class="product-info">
                    <div class="block-title">
                        {{ $product['title'] }}
                    </div>
                    <div class="us-text">
                        {{ $product['short'] }}
                    </div>
                </div>
                {{ $product['desc'] }}
    </div>
</div>
@stop
@section('scripts')
@stop
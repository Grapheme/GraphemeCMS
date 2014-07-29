<?php
    $products = Product::where('publication',1)->with('meta')->with('images')->get();
?>
@if($products->count())
<section class="buy-offer">
    <section class="buy-suboffer">
        <h1>{{ $page_name }}</h1>
        <ul class="sub-offers-ul">
        @foreach($products as $product)
            <li class="sub-offers-li">
            @if(File::exists(public_path('uploads/galleries/'.$product->images['name'])))
            <div class="sub-offers-li-head" style="background: url({{ asset('uploads/galleries/thumbs/'.$product->images['name']) }}) no-repeat 0 0 / cover;">
               <a href="{{ link::to(ProductionController::$prefix_url.'/'.$product->meta->first()->seo_url) }}"></a>
            </div>
            @endif
            <div class="sub-offers-li-body">
                <h3 class="sub-offers-li-header">
                    <a href="{{ link::to(ProductionController::$prefix_url.'/'.$product->meta->first()->seo_url) }}">{{ $product->meta->first()->title }}</a>
                </h3>
                <div class="sub-offers-li-desc">
                    {{ $product->meta->first()->preview }}
                </div>
            </div>
        @endforeach
        </ul>
    </section>
</section>
@endif
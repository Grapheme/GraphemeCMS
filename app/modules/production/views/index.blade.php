<?php
if($products = Product::orderBy('title')->with('images')->get()):
    $products = $products->toArray();
endif;
?>

<div class="wrapper">
    <div class="us-block">
        <ul class="products">
            @foreach($products as $product)
            <li>
                <a class="product-photo" href="{{ link::to('catalog/'.$product['link']) }}" style="background-image: url(uploads/galleries/{{ $product['images']['name'] }});"></a>
                <div class="product-info">
                    <div class="block-title">
                        <a href="{{ link::to('catalog/'.$product['link']) }}">{{ $product['title'] }}</a>
                    </div>
                    <div class="us-text">
                        {{ $product['short'] }}
                    </div>
                </div>
            @endforeach
        </ul>
    </div>
</div>
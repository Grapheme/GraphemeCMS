<?php
if (Input::has('query')):
    $result = SphinxsearchController::search(Input::get('query'));
    $totalCount = (int) count($result['channels']) + (int) count($result['products']) + (int) count($result['reviews']) + (int) count($result['pages']);
    if($totalCount == 0):
        $totalCount = 'Ничего не найдено';
    endif;
endif;
?>

<div class="wrapper">
    <div class="us-block">
        <div class="us-title">Результаты поиска <span class="search-am">({{ $totalCount }})</span></div>
        <ol class="num-list search-list">
            @if(!is_null($result['channels']) && $result['channels']->count())
                @foreach($result['channels'] as $channel)
            <li>
                <div class="search-text">
                    <strong>{{ $channel->title }}</strong><br />
                    {{ $channel->short }}
                </div>
                @if(!empty($channel->link))
                <a href="{{ $channel->link }}" class="post-link">Подробнее</a>
                @endif
                @endforeach
            @endif
            @if(!is_null($result['products']) && count($result['products']))
                @foreach($result['products'] as $product)
            <li>
                <div class="search-text">
                    <div class="product-info">
                        <div class="block-title">{{ $product['title'] }}</div>
                        <div class="us-text">
                            {{ $product['short'] }}
                        </div>
                        {{ $product['desc'] }}
                    </div>
                </div>
                @endforeach
            @endif
            @if(!is_null($result['reviews']) && $result['reviews']->count())
                @foreach($result['reviews'] as $review)
            <li>
                <div class="search-text">
                    <div class="name">{{ $review->name }}</div>
                    <div class="post">{{ $review->position }}</div>
                    <div class="message">{{ $review->content }}</div>
                </div>
                @endforeach
            @endif
            @if(!is_null($result['pages']) && $result['pages']->count())
                @foreach($result['pages'] as $page)
            <li>
                <div class="search-text">
                    {{ $page->metas->first()->name }}
                </div>
                <a href="{{ $page->slug }}" class="post-link">Подробнее</a>
                @endforeach
            @endif
        </ol>
    </div>
</div>
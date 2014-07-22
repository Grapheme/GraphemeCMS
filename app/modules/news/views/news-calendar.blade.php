<div class="row">
@foreach($news as $new)
	<div class="col-sm-6 col-md-4">
        <div class="thumbnail">
          <div class="caption">
            <h3><a href="{{ link::to('news/'.$new->id) }}">{{ $new->title }}</a></h3>
            <p>{{$new->preview}}</p>
          </div>
        </div>
      </div>
@endforeach
</div>
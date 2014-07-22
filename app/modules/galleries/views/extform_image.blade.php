
    <!-- Don't forget add to parent page to scripts section JS code for correct functionality, some like this: -->
    <!-- loadScript("/js/modules/gallery.js"); -->
    @if (@is_object($photo))
    	<input type="text" name="{{ $name }}" value="{{ $photo->id }}" class="uploaded_image_{{ $photo->id }}" style="position:absolute; left:-10000px;" />
    @else
        <input type="text" name="{{ $name }}" value="" class="uploaded_image_false" style="position:absolute; left:-10000px;" />
    @endif
    <div>
    	<div class="egg-dropzone-single dropzone" data-name="{{ $name }}" data-gallery_id="0"<? if (@is_object($photo)) { echo " style='display:none'";} ?>></div>
        <div class="superbox_ photo-preview-container" style="margin-top:10px;">
            @if (@is_object($photo))


            	<div class="photo-preview photo-preview-single" style="background-image:url({{ $photo->thumb() }});">
            		<a href="{{ $photo->path() }}" target="_blank" title="Полноразмерное изображение" style="display:block; height:100%; color:#090; background:transparent"></a>
            		<a href="#" class="photo-delete-single" data-photo-id="{{ $photo->id }}" style="">Удалить</a>
            	</div>
            @else

            	<div class="photo-preview photo-preview-single" style="display:none;">
            		<a href="#photo-path" target="_blank" title="Полноразмерное изображение" class="photo-full-link" style="display:block; height:100%; color:#090; background:transparent"></a>
            		<a href="#" class="photo-delete-single" data-photo-id="#photo-id" style="">Удалить</a>
            	</div>
            @endif
        </div>
    </div>
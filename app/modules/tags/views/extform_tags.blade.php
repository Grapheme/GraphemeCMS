
    <!-- Don't forget add to page JS code for tags functionality, some like this: -->
    <!-- loadScript("/js/plugin/bootstrap-tags/bootstrap-tagsinput.min.js"); -->
    <input name="{{ @$name }}" value="{{ @$value }}" data-role="tagsinput" class="tagsinput"<? if(is_array($params) && @count($params)) { foreach($params as $key => $value) { echo " {$key}=\"{$value}\""; } } ?> />
    
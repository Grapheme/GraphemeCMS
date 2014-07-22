
<aside id="left-panel">
	<nav>
		<ul>
    	@foreach(SystemModules::getSidebarModules() as $name => $module)
<? #echo (string)Request::segment(2) . " == " . $module['link'] . " > " . (int)((string)Request::segment(2) == $module['link']); ?>
<? #dd(Request::segment(2)); ?>
<?
#Helper::d($_SERVER['REQUEST_URI']);
#Helper::d("/" . AuthAccount::getStartPage($module['link']));
#Helper::d( (string)Request::segment(2)."/".(string)Request::segment(3) . " == " . $module['link'] );
$class = false;
if (
    $module['link'] == (string)Request::segment(2) ## pages
    || $module['link'] == (string)Request::segment(2)."/".(string)Request::segment(3) ## 48hours/places
    #|| @$_SERVER['REQUEST_URI'] == "/" . AuthAccount::getStartPage($module['link']) ## admin/48hours/places
)
    $class = "active open";
?>
			<li class="{{ $class }}">
				<a href="{{ link::auth($module['link']) }}" title="{{{ $module['title'] }}}"{{ (isset($module['menu_child']) && !empty($module['menu_child']) && $module['link'] == '#') ? ' onclick="return false;"' : '' }}>
					<i class="fa fa-lg fa-fw {{ $module['class'] }}"></i> <span class="menu-item-parent">{{{ $module['title'] }}}</span>
				</a>
			    @if(isset($module['menu_child']) && is_array($module['menu_child']) && count($module['menu_child']))
				<ul{{ ((string)Request::segment(2) == $module['link']) ? ' style="display:block;"' : '' }}>
				    @foreach($module['menu_child'] as $child_name => $child_module)
<? #echo Request::segment(3) . " == " . $child_module['link'] . " > " . (int)(Request::segment(3) == $child_module['link']); ?>
					<li{{
                        (
                            (Request::segment(2) != '' && Request::segment(2) == $child_module['link'])
                            || (Request::segment(2) != '' && Request::segment(3) != '' && Request::segment(2)."/".Request::segment(3) == $child_module['link'])
                        )
                        ? ' class="active"'
                        : ''
                        }}>
						<a href="{{ link::auth($child_module['link']) }}" title="{{{ $child_module['title'] }}}">
							<i class="fa fa-lg fa-fw {{ $child_module['class'] }}"></i> <span class="menu-item-parent">{{{ $child_module['title'] }}}</span>
						</a>
					</li>
				    @endforeach
				</ul>
			    @endif
			</li>
    	@endforeach
		</ul>
	</nav>
	<span class="minifyme"> <i class="fa fa-arrow-circle-left hit"></i> </span>
</aside>

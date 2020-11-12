<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="_desktop" class="menu_close" style="background-image: url('<?php echo $tpl->_url.'files/uploads/'.$tpl->config('dashboard', 'desktop_background'); ?>');">
	<div id="_menu_shortcut">
		<a href="#"><i class="fa fa-fw fa-cog"></i>Item 1</a>
		<a href="#"><i class="fa fa-fw fa-cog"></i>Item 2</a>
		<a href="#"><i class="fa fa-fw fa-cog"></i>Item 3 Item Item Item Item Item Item Item Item Item Item Item Item Item Item Item Item </a>
	</div>
	<div id="_iframes">
		<div class="_iframes" data-id="1">
			<div class="_iframe_head">
				<span class="_iframe_title ellipsis"><i class="fa fa-fw fa-cog"></i>Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1 Window 1</span>
				<a class="_iframe_refresh" href="#"><i class="fa fa-fw fa-refresh"></i></a>
				<a class="_iframe_minimize" href="#"><i class="fa fa-fw fa-window-minimize"></i></a>
				<a class="_iframe_maximize" href="#"><i class="fa fa-fw fa-window-restore"></i></a>
				<a class="_iframe_close" href="#"><i class="fa fa-fw fa-window-close-o"></i></a>
			</div>
			<iframe class="_iframe_body" src="<?php echo $tpl->_url; ?>admin/location"></iframe>
			<div class="_iframe_body_clicker"></div>
		</div>
		<div class="_iframes" data-id="2">
			<div class="_iframe_head">
				<span class="_iframe_title ellipsis"><i class="fa fa-fw fa-cog"></i>Window 2</span>
				<a class="_iframe_refresh" href="#"><i class="fa fa-fw fa-refresh"></i></a>
				<a class="_iframe_minimize" href="#"><i class="fa fa-fw fa-window-minimize"></i></a>
				<a class="_iframe_maximize" href="#"><i class="fa fa-fw fa-window-restore"></i></a>
				<a class="_iframe_close" href="#"><i class="fa fa-fw fa-window-close-o"></i></a>
			</div>
			<iframe class="_iframe_body" src="<?php echo $tpl->_url; ?>admin/user"></iframe>
			<div class="_iframe_body_clicker"></div>
		</div>
	</div>
</div>
<div id="_menu" style="display: none;">
	<div id="_user_info">
		<img src="<?php echo $tpl->_url.'files/uploads/'.$tpl->config('dashboard', 'desktop_background'); ?>">
		<a href="#" class="ellipsis menu_close">Faid Faid Faid Faid Faid Faid Faid Faid Faid Faid Faid Faid </a>
		<a href="#"><i class="fa fa-sign-out fa-fw"></i></a>
	</div>
	<div id="_menu_list">
		<a href="#" class="ellipsis menu_close"><i class="fa fa-fw fa-cog"></i>Item 1</a>
		<a href="#" class="ellipsis menu_close"><i class="fa fa-fw fa-cog"></i>Item 2</a>
		<a href="#" class="ellipsis menu_close"><i class="fa fa-fw fa-cog"></i>Item 3 Item Item Item Item Item Item Item Item Item Item Item Item Item Item Item Item </a>
	</div>
</div>
<div id="_taskbar">
	<a id="_start" href="#"><i class="fa fa-bars fa-fw"></i> <span>Start</span></a>
	<div id="_iframe_toggles">
		<a class="_iframe_toggles" href="#" data-id="1">
			<span class="_iframe_toggle_title"><i class="fa fa-fw fa-cog"></i> Window 1 Window 1 Window 1 Window 1 Window 1</span>
		</a>
		<a class="_iframe_toggles" href="#" data-id="2">
			<span class="_iframe_toggle_title"><i class="fa fa-fw fa-cog"></i> Window 2</span>
		</a>
	</div>
	<span id="_datetime"></span>
</div>
<script type="text/javascript">
	(function() {
		window.addEventListener('load', function() { 
			var _datetime = '<?php echo time(); ?>';
			setInterval(function() {
				_datetime++;
				var date = new Date(parseInt(_datetime)*1000);
				$('#_datetime').html(date.toLocaleTimeString()+'<br>'+date.toDateString());
			}, 1000);
		}, false);
	})();
</script>
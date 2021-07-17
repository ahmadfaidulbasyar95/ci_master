<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$tpl->load->model('_notif_model');
$tpl->_notif_model->load(1); 
?>
<div id="_desktop" class="menu_close" style="background-image: url('<?php echo $tpl->_url.'files/uploads/'.$tpl->config('dashboard', 'desktop_background'); ?>');">
	<?php 
	$shortcut = $tpl->_db_model->getAll('SELECT * FROM `menu` WHERE `position_id`=0 AND `shortcut`=1 AND `active`=1 ORDER BY `orderby`');
	echo $tpl->menu_show(array(0 => $shortcut), array(
		'wrap' => '<div id="_menu_shortcut">[menu]</div>',
		'item' => '<a href="[url]" target="_iframe"><i class="[icon]"></i>[title]</a>',
	));
	?>
	<div id="_iframes">
		<div class="_iframes" data-id="{id}" style="display: none;">
			<div class="_iframe_head">
				<span class="_iframe_title ellipsis">{title}</span>
				<a class="_iframe_refresh" href="#"><i class="fa fa-fw fa-refresh"></i></a>
				<a class="_iframe_minimize" href="#"><i class="fa fa-fw fa-window-minimize"></i></a>
				<a class="_iframe_maximize" href="#"><i class="fa fa-fw fa-window-restore"></i></a>
				<a class="_iframe_close" href="#"><i class="fa fa-fw fa-window-close-o"></i></a>
			</div>
			<iframe class="_iframe_body" src="{url}" allowfullscreen="allowfullscreen" webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe>
			<div class="_iframe_body_clicker"></div>
		</div>
	</div>
</div>
<div id="_menu" style="display: none;">
	<div id="_user_info">
		<img src="<?php echo $tpl->user['image']; ?>" style="background: white;">
		<a href="<?php echo $tpl->_url.'admin/user/profile'; ?>" data-title="<i class='fa fa-fw fa-user-circle'></i>Profile" target="_iframe" class="menu_close">
			<p class="ellipsis" style="margin: 0;"><?php echo $tpl->user['name']; ?></p>
			<p class="ellipsis" style="margin: 0;font-size: small;"><?php echo $tpl->user_ip().' - '.$tpl->user_device(); ?></p>
		</a>
		<a href="<?php echo $tpl->_url.'admin/user/logout'; ?>" onclick="return confirm('Logout ?')"><i class="fa fa-sign-out fa-fw"></i></a>
	</div>
	<?php 
	echo $tpl->menu_show(0, array(
		'wrap'     => '<div id="_menu_list">[menu]</div>',
		'item'     => '<a href="[url]" target="_iframe" class="ellipsis menu_close"><i class="[icon]"></i>[title]</a>',
		'item_sub' => '<a href="[url]" target="_iframe_sub" class="ellipsis"><i class="[icon]"></i>[title]</a>
									<div>
										[submenu]
									</div>',
	)); 
	?>
</div>
<div id="_taskbar">
	<a id="_start" href="#"><i class="fas fa-ellipsis-v fa-fw"></i> <span>Start</span></a>
	<div id="_notif" class="dropup menu_close">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="far fa-fw fa-bell"></i><span class="badge" style="background-color: red;display: none;" id="notif_badge">9+</span></a>
		<ul class="dropdown-menu" id="notif">
			<li>
				<a href="<?php echo $tpl->_url.'admin/user/notif'; ?>" class="text-center" target="_iframe" data-title="Notification"><b>Show All</b></a>
			</li>
		</ul>
	</div>
	<div id="_iframe_toggles">
		<a class="_iframe_toggles" href="#" data-id="{id}" style="display: none;">
			<span class="_iframe_toggle_title">{title}</span>
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
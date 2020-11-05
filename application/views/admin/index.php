<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
		$tpl->css('includes/libraries/bootstrap-3.4.1-dist/css/bootstrap.min.css');
		$tpl->css('includes/libraries/font-awesome-4.7.0/css/font-awesome.min.css');
		$tpl->css('includes/css/style_additional.css');

		$tpl->js('includes/libraries/jquery-3.5.1/jquery.min.js');
		$tpl->js('includes/libraries/bootstrap-3.4.1-dist/js/bootstrap.min.js');
		$tpl->js('includes/js/script_additional.js');
		?>
	</head>
	<body>
		<div id="_desktop" class="menu_close" style="background-image: url('<?php echo $tpl->_url.'files/uploads/'.$tpl->config('dashboard', 'desktop_background'); ?>');">
			<div id="_content">
				<?php echo $tpl->content; ?>
			</div>
			<div id="_iframes"></div>
		</div>
		<div id="_menu" style="display: none;">
			<div>
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
			<a id="_start" href="#"><i class="fa fa-bars fa-fw"></i></a>
		</div>
	</body>
</html>
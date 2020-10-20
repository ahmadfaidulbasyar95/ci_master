<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
		$tpl->css('includes/libraries/bootstrap-4.5.2-dist/css/bootstrap.min.css');
		$tpl->css('includes/libraries/font-awesome-4.7.0/css/font-awesome.min.css');
		$tpl->css('includes/libraries/fontawesome-5-pro-master/css/all.css');
		$tpl->css('includes/css/style_additional.css');

		$tpl->js('includes/libraries/jquery-3.5.1/jquery.min.js');
		$tpl->js('includes/libraries/bootstrap-4.5.2-dist/js/bootstrap.min.js');
		$tpl->js('includes/libraries/popper-1.16.1/popper.min.js');
		$tpl->js('includes/js/script_additional.js');
		?>
	</head>
	<body>
		<div id="_nav_top" class="col-xs-4">
			<div id="_nav_header">
				<a id="_nav_collapse" href="#"><i class="fa fa-bars"></i></a>
				<div>
					<!--  -->
				</div>
			</div>
			<div id="_body">
				<?php echo $tpl->content; ?>
			</div>
		</div>
		<div id="_nav_left" class="col-xs-8">
			<!--  -->
		</div>
	</body>
</html>
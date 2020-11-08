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
		<div id="_content">
			<?php echo $tpl->content; ?>
		</div>
	</body>
</html>
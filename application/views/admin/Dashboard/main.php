<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="_desktop" class="menu_close" style="background-image: url('<?php echo $tpl->_url.'files/uploads/'.$tpl->config('dashboard', 'desktop_background'); ?>');">
	<?php 
	$par_id   = (isset($_GET['id'])) ? intval($_GET['id']) : 0;
	$shortcut = $tpl->_db_model->getAll('SELECT * FROM `menu` WHERE `position_id`=0 AND `par_id`='.$par_id.' AND `active`=1 ORDER BY `orderby`');
	echo $tpl->menu_show(array(0 => $shortcut), array(
		'wrap' => '<div id="_menu_shortcut">[menu]</div>',
		'item' => '<a href="[url]"><i class="[icon]"></i>[title]</a>',
	));
	?>
</div>
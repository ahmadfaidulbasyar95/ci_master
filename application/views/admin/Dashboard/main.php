<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="_desktop" class="menu_close" style="background-image: url('<?php echo $tpl->_url.'files/uploads/'.$tpl->config('dashboard', 'desktop_background'); ?>');">
	<div id="_menu_shortcut">
		<a href="<?php echo $tpl->_url; ?>admin/menu"><i class="fa fa-fw fa-list"></i>Menu Manager</a>
		<a href="<?php echo $tpl->_url; ?>admin/dashboard/config"><i class="fa fa-fw fa-cog"></i>Configuration</a>
	</div>
</div>
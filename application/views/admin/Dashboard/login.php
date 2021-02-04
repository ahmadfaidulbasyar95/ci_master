<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="_desktop" style="background-image: url('<?php echo $tpl->_url.'files/uploads/'.$tpl->config('dashboard', 'login_background'); ?>'); display: table; width: 100%;">
	<div style="display: table-cell; vertical-align: middle;">
		<div class="container" style="max-width: 500px;">
			<form action="" method="POST" role="form" style="background: #000000a3;padding: 15px;">
				<legend style="color: white;">Sign In</legend>
				<div class="form-group">
					<input name="username" type="text" class="form-control" placeholder="Username">
				</div>
				<div class="form-group">
					<input name="password" type="text" class="form-control" placeholder="Password">
				</div>
				<button type="submit" class="btn btn-info btn-block">Submit</button>
			</form>
		</div>
	</div>
</div>
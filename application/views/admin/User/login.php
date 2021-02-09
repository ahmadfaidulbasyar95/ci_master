<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="_desktop" style="background-image: url('<?php echo $tpl->_url.'files/uploads/'.$tpl->config('dashboard', 'login_background'); ?>'); display: table; width: 100%;">
	<div style="display: table-cell; vertical-align: middle;">
		<div class="container" style="max-width: 500px;">
			<form action="" method="POST" role="form" style="background: #000000a3;padding: 15px;">
				<legend style="color: white;">Please Sign In</legend>
				<?php echo $input['msg']; ?>
				<div class="form-group">
					<input name="<?php echo $input['usr']; ?>" type="text" class="form-control" placeholder="Username" required="required">
				</div>
				<div class="form-group">
					<input name="<?php echo $input['pwd']; ?>" type="password" class="form-control" placeholder="Password" required="required">
				</div>
				<input type="hidden" name="token" value="<?php echo $input['token']; ?>">
				<button type="submit" class="btn btn-info btn-block">Submit</button>
			</form>
		</div>
	</div>
</div>
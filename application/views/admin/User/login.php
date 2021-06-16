<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="_desktop" style="background-image: url('<?php echo $tpl->_url.'files/uploads/'.$tpl->config('dashboard', 'login_background'); ?>'); display: table; width: 100%;">
	<div style="display: table-cell; vertical-align: middle;">
		<div class="container" style="max-width: 500px;">
			<div class="row">
				<div class="col-xs-12">
					<img src="<?php echo $tpl->_url.'files/uploads/'.$tpl->config('site','logo'); ?>" class="img-responsive" alt="Image" style="padding: 20px 20% 20% 20%;-webkit-filter: drop-shadow(0 0px 15px white);">
				</div>
			</div>
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
				<div class="form-group" style="text-align: center;">
					<h4 style="color: white;">Or</h4>
					<a class="btn btn-default btn-block" href="<?php echo $tpl->_url.$tpl->config('dashboard', 'login_uri').'?acc=google'; ?>">Sign In With <i class="fa fa-google"></i>oogle</a>
				</div>
			</form>
		</div>
	</div>
</div>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$tpl->setLayout('blank');
?>
<div id="_desktop" style="background-image: url('<?php echo $tpl->_url.'files/uploads/'.$tpl->config('user', 'login_background'); ?>'); display: table; width: 100%;">
	<div style="display: table-cell; vertical-align: middle;">
		<div class="container" style="max-width: 500px;">
			<div class="row">
				<div class="col-xs-12">
					<a href="<?php echo $tpl->_url; ?>">
						<img src="<?php echo $tpl->_url.'files/uploads/'.$tpl->config('site','logo'); ?>" class="img-responsive" alt="Image" style="padding: 20px 20% 20% 20%;-webkit-filter: drop-shadow(0 0px 15px white);">
					</a>
				</div>
			</div>
			<form action="" method="POST" role="form" style="background: #000000a3;padding: 15px;">
				<legend style="color: white;">Lupa Password</legend>
				<?php echo $input['msg']; ?>
				<div class="form-group">
					<input name="code" type="text" class="form-control" placeholder="Code" required="required">
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-info btn-block">Submit</button>
				</div>
				<p style="color: white">Sudah ingat ? <a href="<?php echo $tpl->_url.'user/login'; ?>" style="color: white"><b>masuk disini</b></a></p>
			</form>
		</div>
	</div>
</div>
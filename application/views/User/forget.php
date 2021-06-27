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
				<?php 
				echo $input['msg']; 
				switch ($input['act']) {
					case 'search':
						?>
						<div class="form-group">
							<input name="search" type="text" class="form-control" placeholder="Cari Akun" required="required">
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-info btn-block">Submit</button>
						</div>
						<?php 
						break;

					case 'send':
						?>
						<div class="form-group text-center">
							<div>
								<img src="<?php echo $input['dt']['image']; ?>" style="height: 100px; width: 100px; object-fit: cover; border-radius: 50%; background: white;">
							</div>
							<h5 style="color: white;"><?php echo $input['dt']['name']; ?></h5>
						</div>
						<div class="form-group">
							<label style="color: white;">Kirim Kode Ke</label>
							<div class="radio">
								<label style="color: white;">
									<input type="radio" name="acc" required="required" value="email"> <?php echo $input['dt']['email']; ?>
								</label>
							</div>
						</div>
						<div class="form-group">
							<div class="radio">
								<label style="color: white;">
									<input type="radio" name="acc" required="required" value="phone"> <?php echo $input['dt']['phone']; ?>
								</label>
							</div>
						</div>
						<div class="form-group">
							<input type="hidden" name="search" value="<?php echo $input['dt']['search']; ?>">
							<a href="<?php echo $tpl->_url.'user/forget'; ?>" class="btn btn-default"><i class="fa fa-chevron-left"></i></a>
							<button type="submit" class="btn btn-info pull-right" style="width: calc(100% - 50px);">Submit</button>
						</div>
						<?php 
						break;

					case 'validate':
						?>
						<div class="form-group text-center">
							<div>
								<img src="<?php echo $input['dt']['image']; ?>" style="height: 100px; width: 100px; object-fit: cover; border-radius: 50%; background: white;">
							</div>
							<h5 style="color: white;"><?php echo $input['dt']['name']; ?></h5>
						</div>
						<div class="form-group">
							<label style="color: white;">Kode</label>
							<input type="text" name="code" class="form-control" value="" required="required">
						</div>
						<div class="form-group">
							<a href="<?php echo $tpl->_url.'user/forget?reset=1'; ?>" class="btn btn-default" onclick="return confirm('Batalkan ?')"><i class="fa fa-chevron-left"></i></a>
							<button type="submit" class="btn btn-info pull-right" style="width: calc(100% - 50px);">Submit</button>
						</div>
						<?php 
						break;

					case 'success':
						?>
						<div class="form-group text-center">
							<div>
								<img src="<?php echo $input['dt']['image']; ?>" style="height: 100px; width: 100px; object-fit: cover; border-radius: 50%; background: white;">
							</div>
							<h5 style="color: white;"><?php echo $input['dt']['name']; ?></h5>
						</div>
						<div class="form-group">
							<h4 style="color: white;">Berhasil !</h4>
							<h5 style="color: white;">Kami mengirim password baru anda ke alamat yang sama dimana anda menerima Kode Lupa Password</h5>
						</div>
						<div class="form-group">
							<a href="<?php echo $tpl->_url.'user/forget?reset=1'; ?>" class="btn btn-default"><i class="fa fa-chevron-left"></i></a>
						</div>
						<?php 
						break;
				}
				?>
				<p style="color: white">Sudah ingat ? <a href="<?php echo $tpl->_url.'user/login'; ?>" style="color: white"><b>masuk disini</b></a></p>
			</form>
		</div>
	</div>
</div>
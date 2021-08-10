<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (isset($_GET['v'])) {
	$v = $_GET['v'];
	if ($v) {
		$this->load->model('_encrypt_model');
		$v = $this->_encrypt_model->decodeToken($v);
		if ($v) {
			$v = $this->_pea_model->_root.$v;
			echo file_get_contents($v);
		}
	}
}
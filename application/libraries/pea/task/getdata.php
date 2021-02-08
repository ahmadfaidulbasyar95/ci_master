<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!empty($_POST['token'])) {
	$this->load->model('_encrypt_model');
	$token = $this->_encrypt_model->decodeToken($_POST['token']);
	if ($token) {
		unset($_POST['token']);
		foreach ($_POST as $key => $value) {
			$token = str_replace('['.$key.']', addslashes($value), $token);
		}
		$this->load->model('_db_model');
		$data = $this->_db_model->getAll($token);
		include_once __DIR__.'/../../output.php';
		lib_output_json($data);
	}
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!empty($_POST['token'])) {
	$this->load->model('_encrypt_model');
	$token = $this->_encrypt_model->decodeToken($_POST['token']);
	if ($token) {
		unset($_POST['token']);
		foreach ($_POST as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $key1 => $value1) {
					$value[$key1] = addslashes($value1);
				}
				$value = '"'.implode('","', $value).'"';
			}else{
				$value = addslashes($value);
			}
			$token = str_replace('['.$key.']', $value, $token);
		}
		$this->load->model('_db_model');
		$data = $this->_db_model->getAll($token);
		include_once __DIR__.'/../../output.php';
		lib_output_json($data);
	}
}
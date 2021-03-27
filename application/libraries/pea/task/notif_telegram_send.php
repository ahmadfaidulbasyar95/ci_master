<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/../../path.php';

$path = $this->_pea_model->_root.'files/notif_telegram/';
$data = lib_path_list($path);

if ($data) {
	$data_send = array();
	for ($i=0; $i < 20; $i++) { 
		if (isset($data[$i])) {
			$data_file = json_decode(file_get_contents($path.$data[$i]), 1);
			if ($data_file) {
				$data_send[] = $data_file;
				unlink($path.$data[$i]);
			}
		}
	}
	if ($data_send) {
		$this->load->model('_tpl_model');

		$telegram_conf = $this->_tpl_model->config('notif_telegram');
		
		$ch = curl_init('https://api.telegram.org/bot'.$telegram_conf['token'].'/sendMessage');
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		foreach ($data_send as $value) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $value);
			$result = curl_exec($ch);
		}
		
		curl_close($ch);
	}
}
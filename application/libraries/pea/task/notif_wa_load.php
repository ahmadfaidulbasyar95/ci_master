<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/../../path.php';

$path = $this->_pea_model->_root.'files/notif_wa/';
$data = lib_path_list($path);

if ($data) {
	$data_send = array();
	for ($i=0; $i < 10; $i++) { 
		if (isset($data[$i])) {
			$data_file = json_decode(file_get_contents($path.$data[$i]), 1);
			if ($data_file) {
				$data_send[] = $data_file;
				unlink($path.$data[$i]);
			}
		}
	}
	if ($data_send) {
		$media_tpls = json_decode(file_get_contents($this->_pea_model->_root.'files/uploads/media_template'), 1);

		$data_out = array();
		foreach ($data_send as $value) {
			if (isset($media_tpls[$value['tpl_name']])) {
				$value_message = $media_tpls[$value['tpl_name']]['message'];
				foreach ($value['data'] as $key1 => $value1) {
					$value_message = str_replace('['.$key1.']', $value1, $value_message);
				}
				$data_out[] = array(
					'phone' => $value['to'],
					'text'  => $value_message
				);
			}
		}

		include_once __DIR__.'/../../output.php';
		lib_output_json($data_out);
	}
}
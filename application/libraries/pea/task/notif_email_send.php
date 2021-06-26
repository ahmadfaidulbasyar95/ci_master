<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/../../path.php';

$path = $this->_pea_model->_root.'files/notif_email/';
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
		$this->load->library('email');

		$email_conf = $this->_tpl_model->config('email');
		$email_tpls = json_decode(file_get_contents($this->_pea_model->_root.'files/uploads/email_template'), 1);
		
		$email_conf_load = array(
			'protocol' => $email_conf['protocol'],
			'newline'  => "\r\n"
		);
		if ($email_conf['protocol'] == 'smtp') {
			$email_conf_load['smtp_host']    = $email_conf['smtp_host'];
			$email_conf_load['smtp_user']    = $email_conf['smtp_user'];
			$email_conf_load['smtp_pass']    = $email_conf['smtp_pass'];
			$email_conf_load['smtp_port']    = $email_conf['smtp_port'];
			$email_conf_load['smtp_timeout'] = $email_conf['smtp_timeout'];
			$email_conf_load['smtp_crypto']  = $email_conf['smtp_crypto'];
		}

		foreach ($data_send as $value) {
			if (isset($email_tpls[$value['tpl_id']])) {
				$value_tpl                   = $email_tpls[$value['tpl_id']];
				$email_conf_load['mailtype'] = ($value_tpl['mailtype'] == 1) ? 'text' : 'html';
				
				$this->email->initialize($email_conf_load);
				$this->email->from(($value_tpl['from_email']) ? $value_tpl['from_email'] : $email_conf['from_email'], ($value_tpl['from_name']) ? $value_tpl['from_name'] : $email_conf['from_name']);
				$this->email->to($value['to']);
				$this->email->subject($value_tpl['subject']);

				$value_message = $value_tpl['message'];

				foreach ($value['data'] as $key1 => $value1) {
					$value_message = str_replace('['.$key1.']', $value1, $value_message);
				}

				$this->email->message($value_message);

				$this->email->send();
				// echo $this->email->print_debugger();
			}
		}
	}
}
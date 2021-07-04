<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->model('_db_model');

$data = $this->_db_model->getAll('SELECT `id`,`title`,`info`,`user_id`,`group_id` FROM `user_notif` WHERE `wa`=0 LIMIT 10');

if ($data) {
	include_once __DIR__.'/../../file.php';
	$path      = $this->_pea_model->_root.'files/notif_wa/'.date('YmdHis').'-';
	$notif_ids = array(); 
	foreach ($data as $value) {
		$notif_ids[] = $value['id'];
		$users       = array();
		if ($value['user_id']) {
			$user = $this->_db_model->getOne('SELECT `phone` FROM `user` WHERE `id`='.$value['user_id'].' AND `phone` != ""');
			if ($user) {
				$users[$user] = 1;
			}
		}
		if ($value['group_id']) {
			$user = $this->_db_model->getCol('SELECT `phone` FROM `user` WHERE `group_ids` LIKE "%\"'.$value['group_id'].'\"%" AND `phone` != ""');
			foreach ($user as $value1) {
				$users[$value1] = 1;
			}
		}
		if ($users) {
			$users = array_keys($users);
			$msg   = $value['title']."\n".$value['info'];
			foreach ($users as $key1 => $value1) {
				lib_file_write($path.$value['id'].'-'.$key1, json_encode(array(
					'tpl_name' => 'notif_wa',
					'to'       => $value1,
					'data'     => array(
						'message' => $msg
					)
				)));
			}
		}
	}
	$this->_db_model->update('user_notif', ['wa' => 1], '`id` IN('.implode(',', $notif_ids).')');
}
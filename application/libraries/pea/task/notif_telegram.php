<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->model('_db_model');

$data = $this->_db_model->getAll('SELECT `id`,`title`,`info`,`user_id`,`group_id` FROM `user_notif` WHERE `telegram`=0 LIMIT 10');

if ($data) {
	include_once __DIR__.'/../../file.php';
	$path      = $this->_pea_model->_root.'files/notif_telegram/'.date('YmdHis').'-';
	$notif_ids = array(); 
	foreach ($data as $value) {
		$notif_ids[] = $value['id'];
		$users       = array();
		if ($value['user_id']) {
			$user = $this->_db_model->getOne('SELECT `telegram_id` FROM `user` WHERE `id`='.$value['user_id'].' AND `telegram_id` != ""');
			if ($user) {
				$users[$user] = 1;
			}
		}
		if ($value['group_id']) {
			$user = $this->_db_model->getCol('SELECT `telegram_id` FROM `user` WHERE `group_ids` LIKE "%\"'.$value['group_id'].'\"%" AND `telegram_id` != ""');
			foreach ($user as $value1) {
				$users[$value1] = 1;
			}
		}
		if ($users) {
			$users = array_keys($users);
			$msg   = $value['title']."\n".$value['info'];
			foreach ($users as $key1 => $value1) {
				lib_file_write($path.$value['id'].'-'.$key1, json_encode(array(
					'chat_id' => $value1,
					'text'    => $msg
				)));
			}
		}
	}
	$this->_db_model->update('user_notif', ['telegram' => 1], '`id` IN('.implode(',', $notif_ids).')');
}
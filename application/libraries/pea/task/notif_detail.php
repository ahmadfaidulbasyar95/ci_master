<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (isset($_GET['id']) and isset($_GET['type'])) {
	$id   = intval($_GET['id']);
	$type = intval($_GET['type']);
	$this->load->library('session');

	if (!empty($_SESSION['user_login'][$type])) {
		$this->load->model('_db_model');

		$user    = $_SESSION['user_login'][$type];
		$add_sql = ' WHERE (`user_id`='.$user['id'].' OR `group_id` IN('.implode($user['group_ids']).') OR (`user_id`=0 AND `group_id`=0)) AND `id`='.$id.' AND `type`='.$type;
		$output  = $this->_db_model->getRow('SELECT `url`,`status` FROM `user_notif`'.$add_sql);
		
		if ($output) {
			if ($output['status'] == 0) {
				$this->_db_model->update('user_notif', array(
					'status' => 1,
				), $id);
			}
			redirect(base_url().$output['url']);
		}
	}
}
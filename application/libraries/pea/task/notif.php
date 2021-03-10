<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (isset($_POST['type'])) {
	$type = intval($_POST['type']);
	$this->load->library('session');

	if (!empty($_SESSION['user_login'][$type])) {
		$this->load->model('_db_model');

		$user    = $_SESSION['user_login'][$type];
		$add_sql = ' WHERE (`user_id`='.$user['id'].' OR `group_id` IN('.implode($user['group_ids']).') OR (`user_id`=0 AND `group_id`=0)) AND `type`='.$type;
		$output  = array(
			'unread' => $this->_db_model->getAll('SELECT `id`,`title`,`info` FROM `user_notif`'.$add_sql.' AND `status`=0 ORDER BY `id` DESC'),
			'read'   => $this->_db_model->getAll('SELECT `id`,`title`,`info` FROM `user_notif`'.$add_sql.' AND `status`=1 ORDER BY `id` DESC LIMIT 10'),
		);
		
		include_once __DIR__.'/../../output.php';
		lib_output_json($output);
	}
}
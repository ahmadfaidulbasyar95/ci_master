<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class _notif_model extends CI_Model {

	function __construct()
	{
		parent::__construct();

		$this->load->model('_db_model');
	}

	function load()
	{
	}

	function send($user_id = '', $title = '', $info = '', $url = '')
	{
		if ($user_id and $title and $info and $url) {
			$this->_db_model->insert('user_notif', array(
				'user_id' => $user_id,
				'title'   => $title,
				'info'    => $info,
				'url'     => $url,
			));
		}
	}
}

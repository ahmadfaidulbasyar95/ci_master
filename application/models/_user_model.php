<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class _user_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$this->login_type = 'default';
		$this->msg        = '';
		$this->load->model('_db_model');
		$this->load->model('_encrypt_model');
	}

	public function setLoginType($login_type = 'default')
	{
		if ($login_type) $this->login_type = $login_type;
	}

	public function login($username = '', $password = '', $login_type = '')
	{
		$this->setLoginType($login_type);
		$this->logout();
		$this->msg = '';
		$user = $this->_db_model->getRow('SELECT * FROM `_user` WHERE `active`=1 AND `username`="'.addslashes($username).'"');
		if ($user) {
			if ($password == $this->_encrypt_model->decode($user['password'])) {
				$user                                = $this->getDetail($user);
				$_SESSION['user'][$this->login_type] = $user;
			}else $this->msg = 'Invalid Password';
		}else $this->msg = 'User not found';
		return $this->getLogin();
	}

	public function logout($login_type = '')
	{
		$this->setLoginType($login_type);
		if (isset($_SESSION['user'][$this->login_type])) unset($_SESSION['user'][$this->login_type]);
	}

	public function getMsg()
	{
		return $this->msg;
	}

	public function getLogin($login_type = '')
	{
		$this->setLoginType($login_type);
		return @(array)$_SESSION['user'][$this->login_type];
	}

	public function get($user_id = 0, $show_detail = 1)
	{
		$user = array();
		if (is_array($user_id)) {
			$user_ids = array();
			foreach ($user_id as $value) {
				if (intval($value)) $user_ids[] = intval($value);
			}
			if ($user_ids) {
				$user = $this->_db_model->getAll('SELECT * FROM `_user` WHERE `id`IN('.implode(',', $user_ids).')');
				if ($show_detail) {
					foreach ($user as $key => $value) {
						$user[$key] = $this->getDetail($value);
					}
				}
			}
		}else{
			$user = $this->_db_model->getRow('SELECT * FROM `_user` WHERE `id`='.intval($user_id));
			if ($show_detail) $user = $this->getDetail($user);
		}
		return $user;
	}

	function getDetail($user = array())
	{
		if (is_array($user)) {
			$user['group_ids'] = repairExplode(@$user['group_ids']);
			$user['params']    = config_decode(@$user['params']);
		}
		return $user;
	}

}

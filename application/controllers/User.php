<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('_user_model');
	}

	public function _remap($method, $params = array())
	{
		pr($this->_user_model->get(['1']));
	}

}

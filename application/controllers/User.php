<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function _remap($method, $params = array())
	{
		$this->setController(__FILE__, $method, $params);
		if ($this->_controller['file']) include $this->_controller['file'];
		else show_404();
	}

}

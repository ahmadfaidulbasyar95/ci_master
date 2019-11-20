<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function _remap($method, $params = array())
	{
		$this->setTemplate('admin');
		$this->setController(__FILE__, $method, $params, 1);
		if ($this->_controller['file']) include $this->_controller['file'];
		else show_404();
	}

}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->setTemplate('admin');
	}

	public function _remap($method, $params = array())
	{
		if ($method != 'index') {
			$controller = $method;
			$method     = 'index';
			if ((@$params[0])) {
				$method = $params[0];
				unset($params[0]);
				$params = array_values($params);
			}
			$file = dirname(__FILE__).'/'.ucfirst($controller).'/admin/'.$method.'.php';
			if (is_file($file)) include $file;
			else show_404();
		}
	}

}

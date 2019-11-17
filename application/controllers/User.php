<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function _remap($method, $params = array())
	{
		$file = str_replace('.php', '/'.$method.'.php', __FILE__);
		if (is_file($file)) include $file;
		else show_404();
	}

}

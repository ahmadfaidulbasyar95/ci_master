<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function _remap($method = '', $args = array())
	{
		$GLOBALS[CIM_ALIAS]->init($method, $args);
	}

}

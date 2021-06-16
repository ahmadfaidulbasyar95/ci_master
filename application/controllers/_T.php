<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class _T extends CI_Controller 
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('_pea_model');
	}

	public function _remap($task = '', $params = array())
	{
		$this->_pea_model->loadTask($task, $params);
	}
}

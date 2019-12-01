<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class _pea_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('_db_model');
		include_once APPPATH.'libraries/pea/pea.php';
	}

	public function newForm($table)
	{
		return new lib_pea($table, $this->_db_model);
	}
}
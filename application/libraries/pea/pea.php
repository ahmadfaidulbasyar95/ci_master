<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class lib_pea
{
	private $table = '';
	private $db    = '';

	function __construct($table, $db)
	{
		$this->table = $table;
		$this->db    = $db;
	}

	public function initEdit($where = '', $table_id = 'id')
	{
		include_once dirname(__FILE__).'/pea_edit.php';
		$this->edit = new lib_pea_edit(array(
			'table'    => $this->table,
			'table_id' => $table_id,
			'where'    => $where,
			'db'       => $this->db,
			'init'     => ($where) ? 'edit' : 'add',
		));
	}
}
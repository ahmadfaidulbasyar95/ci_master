<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class lib_pea
{
	private $table = '';
	private $db    = '';
	private $_url  = '';
	private $_root = '';

	function __construct($opt)
	{
		$this->table = $opt['table'];
		$this->db    = $opt['db'];
		$this->_url  = $opt['_url'];
		$this->_root = $opt['_root'];
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
			'_url'     => $this->_url,
			'_root'    => $this->_root,
		));
	}

	public function initRoll($where = '', $table_id = 'id')
	{
		include_once dirname(__FILE__).'/pea_roll.php';
		$this->roll = new lib_pea_roll(array(
			'table'    => $this->table,
			'table_id' => $table_id,
			'where'    => $where,
			'db'       => $this->db,
			'init'     => 'roll',
			'_url'     => $this->_url,
			'_root'    => $this->_root,
		));
	}

	public function initSearch($where = '', $table_id = 'id')
	{
		include_once dirname(__FILE__).'/pea_search.php';
		$this->search = new lib_pea_search(array(
			'table'    => $this->table,
			'table_id' => $table_id,
			'where'    => $where,
			'db'       => $this->db,
			'init'     => 'search',
			'_url'     => $this->_url,
			'_root'    => $this->_root,
		));
	}
}
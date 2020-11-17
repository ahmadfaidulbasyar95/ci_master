<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/../bsv.php';
class lib_pea
{
	public $table = '';
	public $db    = '';
	public $_url  = '';
	public $_root = '';

	function __construct($opt)
	{
		$this->table = $opt['table'];
		$this->db    = $opt['db'];
		$this->_url  = $opt['_url'];
		$this->_root = $opt['_root'];
	}

	public function initEdit($where = '', $table_id = 'id', $make_data_exist = 0)
	{
		if ($where and $make_data_exist) { // only edit mode
			if (empty($this->db->getOne('SELECT 1 FROM '.$this->table.' '.$where))) {
				preg_match_all('~([a-zA-Z0-9_]+)`?\s?[=|\+|-|\/|*|<|>]+\s?([0-9,.]+|"(.*?)")~', str_replace('\"', '##PETIK##', $where), $match);
				if ($match) {
					$data = array();
					foreach ($match[1] as $key => $value) {
						if (empty($match[3][$key])) {
							$data[$value] = $match[2][$key];
						}else{
							$data[$value] = str_replace('##PETIK##', '"', $match[3][$key]);
						}
					}
					if ($data) {
						$this->db->insert($this->table, $data);
					}
				}
			}
		}
		include_once __DIR__.'/pea_edit.php';
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
		include_once __DIR__.'/pea_roll.php';
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
		include_once __DIR__.'/pea_search.php';
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

	public function initImport($where = '', $table_id = 'id')
	{
		include_once __DIR__.'/pea_import.php';
		$this->import = new lib_pea_import(array(
			'table'    => $this->table,
			'table_id' => $table_id,
			'where'    => $where,
			'db'       => $this->db,
			'init'     => 'import',
			'_url'     => $this->_url,
			'_root'    => $this->_root,
		));
	}
}
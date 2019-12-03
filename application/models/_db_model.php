<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class _db_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function getAll($query = '')
	{
		$query = $this->db->query($query);
		return @(array)$query->result_array();
	}

	public function getAssoc($query = '')
	{
		$data = array();
		foreach ($this->getAll($query) as $value) {
			$data[array_values($value)[0]] = $value;
		}
		return $data;
	}

	public function getRow($query = '')
	{
		$query = $this->db->query($query);
		return @(array)$query->row_array();
	}

	public function getOne($query = '')
	{
		$query = $this->db->query($query);
		return @array_values($query->row_array())[0];
	}

	public function insert($table = '', $data = array())
	{
		$query = (isset($data[0])) ? $this->db->insert_batch($table, $data): $this->db->insert($table, $data);
		return $query;
	}

	public function update($table = '', $data = array(), $where = array())
	{
		if (is_numeric($where)) {
			$where = array('id' => $where);
		}
		$query = $this->db->update($table, $data, $where);
		return $query;
	}

	public function delete($table = '', $where = array())
	{
		if (is_numeric($where)) {
			$where = array('id' => $where);
		}
		$query = $this->db->delete($table, $where);
		return $query;
	}

}

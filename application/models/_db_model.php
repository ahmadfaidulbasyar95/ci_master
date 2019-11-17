<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class _db_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function getAll($query = '')
	{
		$query = $this->db->query($query);
		return @(array)$query->result_array();
	}

	function getRow($query = '')
	{
		$query = $this->db->query($query);
		return @(array)$query->row_array();
	}

	function getOne($query = '')
	{
		$query = $this->db->query($query);
		return @array_values($query->row_array())[0];
	}

	function insert($table = '', $data = array())
	{
		$query = (isset($data[0])) ? $this->db->insert_batch($table, $data): $this->db->insert($table, $data);
		return $query;
	}

	function update($table = '', $data = array(), $where = array())
	{
		if (is_numeric($where)) {
			$where = array('id' => $where);
		}
		$query = $this->db->update($table, $data, $where);
		return $query;
	}

}

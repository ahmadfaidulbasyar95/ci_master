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

}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class _config_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->model('_db_model');
		$this->data = array();
	}

	public function set($type = '', $name = '', $value = '')
	{
		if ($type and $name) {
			$value      = (is_array($value)) ? config_encode($value) : $value;
			$is_array   = (is_array($value)) ? 1 : 0;
			$current_id = $this->_db_model->getOne('SELECT `id` FROM `_config` WHERE `type`="'.addslashes($type).'" AND `name`="'.$name.'"');
			$data       = array(
				'type'     => $type,
				'name'     => $name,
				'is_array' => $is_array,
				'value'    => $value,
			);
			delCache($type.'__'.$name);
			if (isset($this->data[$type][$name])) unset($this->data[$type][$name]);
			return ($current_id) ? $this->_db_model->update('_config', $data, $current_id) : $this->_db_model->insert('_config', $data);
		}
		return false;
	}

	public function get($type = '', $name = '', $index = '')
	{
		$data = array();
		if ($type and $name) {
			$data = @$this->data[$type][$name];
			if (!$data) {
				$data = getCache($type.'__'.$name);
				if (!$data) {
					$data = $this->_db_model->getRow('SELECT `value`,`is_array` FROM `_config` WHERE `type`="'.addslashes($type).'" AND `name`="'.$name.'"');
					if (@$data['is_array']) {
						$data = config_decode($data['value']);
					}else{
						$data = @$data['value'];
					}
					setCache($type.'__'.$name, $data);
				}
				$this->data[$type][$name] = $data;
			}
		}
		return ($index) ? @$data[$index] : $data;
	}

}

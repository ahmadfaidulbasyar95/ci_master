<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/select.php';
class lib_pea_frm_selecttable extends lib_pea_frm_select
{	
	public $referenceTable      = '';
	public $referenceFieldKey   = '';
	public $referenceFieldValue = '';
	public $referenceCondition  = '';
	public $options_load        = 0;
	public $dependent           = array();

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
	}

	public function setReferenceTable($referenceTable = '')
	{
		if ($referenceTable) $this->referenceTable = $referenceTable;
	}

	public function setReferenceField($key = '', $value = '')
	{
		if ($key) $this->referenceFieldKey     = $key;
		if ($value) $this->referenceFieldValue = $value;
	}

	public function setReferenceCondition($referenceCondition = '')
	{
		if ($referenceCondition) $this->referenceCondition = 'WHERE '.$referenceCondition;
	}

	public function setDependent($name = '', $field = '')
	{
		if ($name and $field) {
			$this->dependent = array(
				'name'  => $name,
				'field' => $field,
			);
			$this->setIncludes('selecttable_dependent', 'js');
		}
	}

	public function getOptionTable()
	{
		if (!$this->options_load) {
			if ($this->dependent) {
				$token = 'SELECT '.$this->referenceFieldKey.' AS `key`, '.$this->referenceFieldValue.' AS `value` FROM '.$this->referenceTable.' '.$this->referenceCondition;
				if ($this->referenceCondition) {
					$token .= ' AND '.$this->dependent['field'].'="[v]"';
				}else{
					$token .= ' WHERE '.$this->dependent['field'].'="[v]"';
				}
				$this->db->load->model('_encrypt_model');
				$token = $this->db->_encrypt_model->encodeToken($token, 30);
				$this->addAttr('data-token="'.$token.'"');
				$this->addAttr('data-dependent="'.$this->dependent['name'].'"');
				$this->addClass('selecttable_dependent');
			}else{
				$option = $this->db->getAll('SELECT '.$this->referenceFieldKey.' AS `key`, '.$this->referenceFieldValue.' AS `value` FROM '.$this->referenceTable.' '.$this->referenceCondition);
				foreach ($option as $value) {
					$this->addOption($value['key'], $value['value']);
				}
			}
			$this->options_load = 1;
		}
	}

	public function getReportOutput($value_ = '')
	{
		$this->getOptionTable();
		return parent::getReportOutput($value_);
	}

	public function getForm($index = '')
	{
		$this->getOptionTable();
		return parent::getForm($index);
	}
}
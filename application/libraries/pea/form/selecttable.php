<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/select.php';
class lib_pea_frm_selecttable extends lib_pea_frm_select
{	
	public $referenceTable      = '';
	public $referenceFieldKey   = '';
	public $referenceFieldValue = '';
	public $referenceCondition  = '';
	public $referenceNested     = '';
	public $referenceOrderBy    = '';
	public $referenceGroupBy    = '';
	public $options_load        = 0;
	public $dependent           = array();

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
	}

	public function getSelectedValue($index = '')
	{
		$value    = $this->getValue($index);
		$selected = array();
		foreach ($this->options as $key => $val) {
			if ($value == $val) $selected[$val] = $key;
		}
		return $selected;
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

	public function setReferenceNested($referenceNested = '')
	{
		if ($referenceNested) $this->referenceNested = $referenceNested;
	}

	public function setReferenceGroupBy($referenceGroupBy = '')
	{
		if ($referenceGroupBy) $this->referenceGroupBy = ' GROUP BY '.$referenceGroupBy;
	}

	public function setReferenceOrderBy($referenceOrderBy = '')
	{
		if ($referenceOrderBy) $this->referenceOrderBy = ' ORDER BY '.$referenceOrderBy;
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

	public function getOptionTable($index = '', $values = array())
	{
		if (!$this->options_load) {
			$nested = ($this->referenceNested) ? ', '.$this->referenceNested.' AS `nested`' : '';
			if ($this->dependent and !$this->isPlainText) {
				if ($this->referenceCondition) {
					$this->referenceCondition .= ' AND '.$this->dependent['field'].'="[v]"';
				}else{
					$this->referenceCondition .= ' WHERE '.$this->dependent['field'].'="[v]"';
				}
				$token = 'SELECT '.$this->referenceFieldKey.' AS `key`, '.$this->referenceFieldValue.' AS `value`'.$nested.' FROM '.$this->referenceTable.' '.$this->referenceCondition.$this->referenceGroupBy.$this->referenceOrderBy;
				$this->db->load->model('_encrypt_model');
				$token = $this->db->_encrypt_model->encodeToken($token, 60);
				$this->addAttr('data-token="'.$token.'"');
				$this->addAttr('data-dependent="'.$this->dependent['name'].'"');
				$this->addAttr('data-nested="'.$this->referenceNested.'"');
				$this->addClass('selecttable_dependent');
			}else{
				if ($this->isPlainText) {
					$ids = [];
					if (is_numeric($index)) {
						if ($this->isMultiselect) {
							foreach ($values as $value) {
								$value = json_decode($value[$this->fieldNameDb]);
								if ($value) {
									foreach ($value as $v) {
										$ids[$v] = 1;
									}
								}
							}
						}else{
							foreach ($values as $value) {
								$ids[$value[$this->fieldNameDb]] = 1;
							}
						}
					}else{
						if ($this->isMultiselect) {
							$value = json_decode($values[$this->fieldNameDb]);
							if ($value) {
								foreach ($value as $v) {
									$ids[$v] = 1;
								}
							}
						}else{
							$ids[$values[$this->fieldNameDb]] = 1;
						}
					}
					if ($ids) {
						$ids = array_keys($ids);
						$ids = '"'.implode('","', $ids).'"';
						if ($this->referenceCondition) {
							$this->referenceCondition .= ' AND '.$this->referenceFieldValue.' IN('.$ids.')';
						}else{
							$this->referenceCondition .= ' WHERE '.$this->referenceFieldValue.' IN('.$ids.')';
						}
					}
				}
				$option = $this->db->getAll('SELECT '.$this->referenceFieldKey.' AS `key`, '.$this->referenceFieldValue.' AS `value`'.$nested.' FROM '.$this->referenceTable.' '.$this->referenceCondition.$this->referenceGroupBy.$this->referenceOrderBy);
				if ($this->referenceNested) {
					$option = $this->getOptionNested($option);
				}
				foreach ($option as $value) {
					$this->addOption($value['key'], $value['value']);
				}
			}
			$this->options_load = 1;
		}
	}

	public function getOptionNested($option = array(), $nested = 0, $prefix = '')
	{
		$option_ = array();
		$prefix_ = ($prefix) ? $prefix.' ' : '';
		foreach ($option as $key => $value) {
			if ($value['nested'] == $nested) {
				$value['key'] = $prefix_.$value['key'];
				$option_[]    = $value;
				unset($option[$key]);
				foreach ($this->getOptionNested($option, $value['value'], $prefix_.'->') as $value_) {
					$option_[] = $value_;
				}
			}
		}
		return $option_;
	}

	public function getSearchSql()
	{
		$this->getOptionTable();
		return parent::getSearchSql();
	}

	public function getReportOutput($value_ = '', $type = '', $index = '', $values = array())
	{
		$this->getOptionTable($index, $values);
		return parent::getReportOutput($value_);
	}

	public function getForm($index = '', $values = array())
	{
		$this->getOptionTable($index, $values);
		return parent::getForm($index, $values);
	}
}
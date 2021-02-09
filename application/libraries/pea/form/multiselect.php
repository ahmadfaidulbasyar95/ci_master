<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/selecttable.php';
class lib_pea_frm_multiselect extends lib_pea_frm_selecttable
{	

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->addAttr('multiple');
		$this->isMultiselect = 1;
	}

	public function getPostValue($index = '')
	{
		$value = parent::getPostValue($index);
		return json_encode($value);
	}

	public function getValue($index = '')
	{
		$value = parent::getValue($index);
		return @(array)json_decode($value, 1);
	}

	public function getSelectedValue($index = '')
	{
		$value    = $this->getValue($index);
		$selected = array();
		foreach ($this->options as $key => $val) {
			if (in_array($val, $value)) $selected[$val] = $key;
		}
		return $selected;
	}

	public function getOption($index = '')
	{
		$options = '';
		$value   = $this->getValue($index);
		foreach ($this->options as $key => $val) {
			$options .= (in_array($val, $value)) ? '<option value="'.$val.'" selected>'.$key.'</option>' : '<option value="'.$val.'">'.$key.'</option>'; 
		}
		return $options;
	}

	public function getOptionTable()
	{
		if ($this->referenceTable) {
			parent::getOptionTable();
		}
	}

	public function getReportOutput($value_ = '')
	{
		$this->getOptionTable();
		return parent::getReportOutput($value_);
	}

	public function getForm($index = '')
	{
		return parent::getForm($index);
	}
}
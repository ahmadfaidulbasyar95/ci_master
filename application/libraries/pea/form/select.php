<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/text.php';
class lib_pea_frm_select extends lib_pea_frm_text
{	
	public $options       = array();
	public $delimiter     = ', ';
	public $delimiter_alt = ', ';

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
	}

	public function setDelimiter($delimiter = '')
	{
		$this->delimiter     = $delimiter;
		$this->delimiter_alt = $delimiter;
	}

	public function setDelimiterAlt($delimiter_alt = '')
	{
		$this->delimiter_alt = $delimiter_alt;
	}

	public function getSelectedValue($index = '')
	{
		$value    = $this->getValue($index);
		$selected = '';
		foreach ($this->options as $key => $val) {
			if ($value == $val) $selected = $key;
		}
		return $selected;
	}

	public function addOption($key = '', $value = '')
	{
		if ($key) $this->options[$key] = $value;
	}

	public function addOptions($options = array())
	{
		foreach ($options as $key => $value) {
			$this->addOption($key, $value);
		}
	}

	public function getOption($index = '')
	{
		$options = '';
		$value   = $this->getValue($index);
		foreach ($this->options as $key => $val) {
			$options .= ((string)$value == (string)$val) ? '<option value="'.$val.'" selected>'.$key.'</option>' : '<option value="'.$val.'">'.$key.'</option>'; 
		}
		return $options;
	}

	public function getReportOutput($value = '')
	{
		foreach ($this->options as $key => $val) {
			if ((string)$value == (string)$val) return $key;
		}
	}

	public function getForm($index = '', $values = array())
	{
		$this->getUniqJS($index);
		$form = '';
		if ($this->init == 'roll' and !$this->isMultiinput) $form .= '<td>';
		$form .= $this->formBefore;
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and !$this->isMultiinput and in_array($this->init, ['edit','add'])) $form .= '<label>'.$this->title.'</label>';
		if ($this->isPlainText) {
			$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getSelectedValue($index), $this->getValueID($index), $index, $values) : $this->getSelectedValue($index);
			if (is_array($value)) {
				$value = implode($this->delimiter, $value);
			}
			$form .= ($this->init == 'roll' or $this->isMultiinput) ? $value : '<p>'.$value.'</p>';
		}else{
			$value = $this->getValue($index);
			if (is_array($value)) {
				$value = json_encode($value);
			}
			$name = (is_numeric($index)) ? $this->name.'['.$index.']' : $this->name;
			$name = ($this->isMultiform) ? $name.'[]' : $name;
			$name = ($this->isMultiselect) ? $name.'[]' : $name;
			$form .= '
<select name="'.$name.'" class="form-control '.$this->attr_class.'" title="'.$this->caption.'" '.$this->attr.' '.$this->isRequire.' data-value=\''.$value.'\'>
	'.$this->getOption($index).'
</select>';
		}
		if ($this->tips) $form .= '<div class="'.lib_bsv('help-block', 'form-text text-muted').'">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		$form .= $this->formAfter;
		if ($this->init == 'roll' and !$this->isMultiinput) $form .= '</td>';
		return $form;
	}
}
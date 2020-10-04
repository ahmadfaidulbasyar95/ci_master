<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/text.php';
class lib_pea_frm_select extends lib_pea_frm_text
{	
	public $options = array();

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
	}

	public function getSelectedValue($index = '')
	{
		$value = $this->getValue($index);
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

	public function getOption($index = '')
	{
		$options = '';
		$value = $this->getValue($index);
		foreach ($this->options as $key => $val) {
			$options .= ($value == $val) ? '<option value="'.$val.'" selected>'.$key.'</option>' : '<option value="'.$val.'">'.$key.'</option>'; 
		}
		return $options;
	}

	public function getReportOutput($value = '')
	{
		foreach ($this->options as $key => $val) {
			if ($value == $val) return $key;
		}
	}

	public function getForm($index = '')
	{
		$form = '';
		if ($this->init == 'roll') $form .= '<td>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and in_array($this->init, ['edit','add'])) $form .= '<label>'.$this->title.'</label>';
		if ($this->isPlainText) {
			$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getSelectedValue($index)) : $this->getSelectedValue($index);
			$form .= ($this->init == 'roll') ? $value : '<p>'.$value.'</p>';
		}else{
			$name = (is_numeric($index)) ? $this->name.'['.$index.']' : $this->name;
			$name = ($this->isMultiform) ? $name.'[]' : $name;
			$form .= '
<select name="'.$name.'" class="form-control" title="'.$this->caption.'" '.$this->attr.' '.$this->isRequire.'>
	'.$this->getOption($index).'
</select>';
		}
		if ($this->tips) $form .= '<div class="help-block">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		if ($this->init == 'roll') $form .= '</td>';
		return $form;
	}
}
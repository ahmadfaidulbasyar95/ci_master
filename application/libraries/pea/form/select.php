<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once dirname(__FILE__).'/text.php';
class lib_pea_frm_select extends lib_pea_frm_text
{	
	public $options = array();

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
	}

	public function getSelectedValue()
	{
		$value = $this->getValue();
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

	public function getOption()
	{
		$options = '';
		$value = $this->getValue();
		foreach ($this->options as $key => $val) {
			$options .= ($value == $val) ? '<option value="'.$val.'" selected>'.$key.'</option>' : '<option value="'.$val.'">'.$key.'</option>'; 
		}
		return $options;
	}

	public function getForm()
	{
		$form = '';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and $this->init != 'roll') $form .= '<label>'.$this->title.'</label>';
		if ($this->isPlainText) {
			$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getSelectedValue()) : $this->getSelectedValue();
			$form .= '<p>'.$value.'</p>';
		}else{
			$name = ($this->isMultiform) ? $this->name.'[]' : $this->name;
			$form .= '
<select name="'.$name.'" class="form-control" title="'.$this->caption.'" '.$this->isRequire.'>
	'.$this->getOption().'
</select>';
		}
		if ($this->tips) $form .= '<div class="help-block">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		return $form;
	}
}
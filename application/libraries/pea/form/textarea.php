<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once dirname(__FILE__).'/text.php';
class lib_pea_frm_textarea extends lib_pea_frm_text
{	
	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
	}

	public function getForm($index = '')
	{
		$form = '';
		if ($this->init == 'roll') $form .= '<td>';
		if ($this->init == 'search') $form .= '&nbsp;';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and in_array($this->init, ['edit','add'])) $form .= '<label>'.$this->title.'</label>';
		if ($this->isPlainText) {
			$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getValue($index)) : $this->getValue($index);
			$form .= ($this->init == 'roll') ? $value : '<p>'.$value.'</p>';
		}else{
			$name = (is_numeric($index)) ? $this->name.'['.$index.']' : $this->name;
			$name = ($this->isMultiform) ? $name.'[]' : $name;
			$form .= '<textarea name="'.$name.'" class="form-control" title="'.$this->caption.'" placeholder="'.$this->caption.'" '.$this->isRequire.'>'.$this->getValue($index).'</textarea>';
		}
		if ($this->tips) $form .= '<div class="help-block">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		if ($this->init == 'roll') $form .= '</td>';
		return $form;
	}
}
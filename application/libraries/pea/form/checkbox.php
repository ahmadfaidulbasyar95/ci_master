<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once dirname(__FILE__).'/text.php';
class lib_pea_frm_checkbox extends lib_pea_frm_text
{	
	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
	}

	public function getPostValue($index = '')
	{
		return (is_numeric($index)) ? @intval($_POST[$this->getName()][$index]) : @intval($_POST[$this->getName()]);
	}

	public function getForm($index = '')
	{
		$form = '';
		if ($this->init == 'roll') $form .= '<td>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and $this->init != 'roll') $form .= '<label>'.$this->title.'</label>';
		if ($this->isPlainText) {
			$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getValue($index)) : $this->getValue($index);
			$form .= '<p>'.$value.'</p>';
		}else{
			$name = (is_numeric($index)) ? $this->name.'['.$index.']' : $this->name;
			$name = ($this->isMultiform) ? $name.'[]' : $name;
			$value = ($this->getValue($index)) ? 'checked="checked"' : '';
			$form .= '
<div class="checkbox">
	<label>
		<input type="checkbox" name="'.$name.'" value="1" title="'.$this->caption.'" '.$value.'>
		'.$this->caption.'
	</label>
</div>';
		}
		if ($this->tips) $form .= '<div class="help-block">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		if ($this->init == 'roll') $form .= '</td>';
		return $form;
	}
}
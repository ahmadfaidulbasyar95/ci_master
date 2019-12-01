<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once dirname(__FILE__).'/text.php';
class lib_pea_frm_checkbox extends lib_pea_frm_text
{	
	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
	}

	public function getPostValue()
	{
		return @intval($_POST[$this->getName()]);
	}

	public function getForm()
	{
		$form = '';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and $this->init != 'roll') $form .= '<label>'.$this->title.'</label>';
		if ($this->isPlainText) {
			$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getValue()) : $this->getValue();
			$form .= '<p>'.$value.'</p>';
		}else{
			$name  = ($this->isMultiform) ? $this->name.'[]' : $this->name;
			$value = ($this->getValue()) ? 'checked="checked"' : '';
			$form .= '
<div class="checkbox">
	<label>
		<input type="checkbox" name="'.$name.'" value="1" title="'.$this->caption.'" placeholder="'.$this->caption.'" '.$value.'>
		'.$this->caption.'
	</label>
</div>';
		}
		if ($this->tips) $form .= '<div class="help-block">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		return $form;
	}
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/text.php';
class lib_pea_frm_hidden extends lib_pea_frm_text
{	
	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->setType('hidden');
	}

	public function getForm($index = '', $values = array())
	{
		$form = '';
		if (!$this->isPlainText) {
			$name = (is_numeric($index)) ? $this->name.'['.$index.']' : $this->name;
			$name = ($this->isMultiform) ? $name.'[]' : $name;
			$form .= '<input type="'.$this->type.'" name="'.$name.'" class="form-control '.$this->attr_class.'" value="'.htmlentities($this->getValue($index)).'" title="'.$this->caption.'" placeholder="'.$this->caption.'" '.$this->attr.' '.$this->isRequire.'>';
		}
		return $form;
	}
}
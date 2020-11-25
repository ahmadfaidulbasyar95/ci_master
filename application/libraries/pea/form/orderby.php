<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/text.php';
class lib_pea_frm_orderby extends lib_pea_frm_text
{	
	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->setIncludes('orderby', 'js');
	}

	public function setValue($value = '', $index = '')
	{
		$this->value_roll[$index] = $index+1;
	}

	public function getForm($index = '')
	{
		$form = '';
		$form .= '<td>';
		if ($this->isPlainText) {
			$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getValue($index)) : $this->getValue($index);
			$form .= $value;
		}else{
			$name = $this->name.'[]';
			$form .= '<input type="hidden" name="'.$name.'" class="form-control" value="'.$this->getValue($index).'" title="'.$this->caption.'" placeholder="'.$this->caption.'" '.$this->attr.'>';
			$form .= '<span class="orderby_button btn btn-default btn-xs" title="'.$this->caption.'"><i class="fa fa-exchange fa-fw" style="transform: rotate(90deg);"></i></span>';
		}
		$form .= '</td>';
		return $form;
	}
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once dirname(__FILE__).'/text.php';
class lib_pea_frm_datetime extends lib_pea_frm_text
{	
	public $dateFormat = 'Y-m-d H:i:s';

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->setType('datetime');
	}

	public function getDateValue()
	{
		return date($this->dateFormat, strtotime($this->getValue()));
	}

	public function setDateFormat($dateFormat = 'Y-m-d H:i:s')
	{
		$this->dateFormat = $dateFormat;
	}

	public function getForm()
	{
		$form = '';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and $this->init != 'roll') $form .= '<label>'.$this->title.'</label>';
		if ($this->isPlainText) {
			$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getDateValue()) : $this->getDateValue();
			$form .= '<p>'.$value.'</p>';
		}else{
			$name = ($this->isMultiform) ? $this->name.'[]' : $this->name;
			$form .= '<input type="'.$this->type.'" name="'.$name.'" class="form-control" value="'.$this->getDateValue().'" title="'.$this->caption.'" placeholder="'.$this->caption.'" '.$this->isRequire.'>';
		}
		if ($this->tips) $form .= '<div class="help-block">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		return $form;
	}
}
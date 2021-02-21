<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/text.php';
class lib_pea_frm_datetime extends lib_pea_frm_text
{	
	public $dateFormat = 'Y-m-d H:i:s';

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->setType('datetime');
	}

	public function getDateValue($index = '')
	{
		$value = $this->getValue($index);
		if ($value and $value != '0000-00-00') {
			return date($this->dateFormat, strtotime($value));
		}else{
			return '';
		}
	}

	public function setDateFormat($dateFormat = 'Y-m-d H:i:s')
	{
		$this->dateFormat = $dateFormat;
	}

	public function getReportOutput($value = '')
	{
		if ($value and $value != '0000-00-00') {
			return date($this->dateFormat, strtotime($value));
		}else{
			return '';
		}
	}

	public function getForm($index = '', $values = array())
	{
		$form = '';
		if ($this->init == 'roll' and !$this->isMultiinput) $form .= '<td>';
		$form .= $this->formBefore;
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and !$this->isMultiinput and in_array($this->init, ['edit','add'])) $form .= '<label>'.$this->title.'</label>';
		if ($this->isPlainText) {
			$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getDateValue($index), $this->getValueID($index), $index, $values) : $this->getDateValue($index);
			$form .= ($this->init == 'roll' or $this->isMultiinput) ? $value : '<p>'.$value.'</p>';
		}else{
			$name = (is_numeric($index)) ? $this->name.'['.$index.']' : $this->name;
			$name = ($this->isMultiform) ? $name.'[]' : $name;
			$form .= '<input type="'.$this->type.'" name="'.$name.'" class="form-control '.$this->attr_class.'" value="'.$this->getDateValue($index).'" title="'.$this->caption.'" placeholder="'.$this->caption.'" '.$this->attr.' '.$this->isRequire.'>';
		}
		if ($this->tips) $form .= '<div class="'.lib_bsv('help-block', 'form-text text-muted').'">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		$form .= $this->formAfter;
		if ($this->init == 'roll' and !$this->isMultiinput) $form .= '</td>';
		return $form;
	}
}
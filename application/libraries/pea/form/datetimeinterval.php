<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/datetime.php';
class lib_pea_frm_datetimeinterval extends lib_pea_frm_datetime
{	
	public $endDateField = '';

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->setDateConfig('singleDatePicker', false);
	}

	public function getDateValue($index = '')
	{
		$value = $this->getValue($index);
		$value = $this->getValueInterval($value);
		foreach ($value as $key => $value1) {
			if (intval($value1)) {
				$value[$key] = date($this->dateFormat, strtotime($value1));
			}else{
				$value[$key] = '';
			}
		}
		return implode(' - ', $value);
	}

	public function getValueInterval($value = '')
	{
		return explode('|', $value);
	}

	public function setEndDateField($endDateField = '')
	{
		$this->endDateField = $endDateField;
		$this->setFieldName('CONCAT(`'.$this->getFieldName().'`,"|",`'.$endDateField.'`)');
	}

	public function getPostValue($index = '')
	{
		$value  = (is_numeric($index)) ? @$_POST[$this->getName()][$index] : @$_POST[$this->getName()];
		$value_ = (is_numeric($index)) ? @$_POST[$this->getName().'_end'][$index] : @$_POST[$this->getName().'_end'];
		if ($this->getRequire()) {
			if (!$value or !$value_) {
				$this->msg = str_replace('{msg}', str_replace('{title}', $this->title, @$this->failMsg['require']), $this->failMsgTpl);
			}
		}
		return array(
			$this->fieldNameDb  => $value,
			$this->endDateField => $value_,
		);
	}

	public function getReportOutput($value = '')
	{
		$value = $this->getValueInterval($value);
		foreach ($value as $key => $value1) {
			if (intval($value1)) {
				$value[$key] = date($this->dateFormat, strtotime($value1));
			}else{
				$value[$key] = '';
			}
		}
		return implode(' - ', $value);
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
			$name  = (is_numeric($index)) ? $this->name.'['.$index.']' : $this->name;
			$name  = ($this->isMultiform) ? $name.'[]' : $name;
			$name_ = (is_numeric($index)) ? $this->name.'_end['.$index.']' : $this->name.'_end';
			$name_ = ($this->isMultiform) ? $name_.'[]' : $name_;
			$value = $this->getValue($index);
			$value = $this->getValueInterval($value);
			$form .= '<input type="hidden" name="'.$name_.'" value="'.@$value[1].'">';
			$form .= '<input type="hidden" name="'.$name.'" value="'.@$value[0].'">';
			$form .= '<input type="'.$this->type.'" class="form-control fm_daterangepicker '.$this->attr_class.'" title="'.$this->caption.'" placeholder="'.$this->caption.'" '.$this->attr.' '.$this->isRequire.' data-config=\''.json_encode($this->dateConfig).'\'>';
		}
		if ($this->tips) $form .= '<div class="'.lib_bsv('help-block', 'form-text text-muted').'">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		$form .= $this->formAfter;
		if ($this->init == 'roll' and !$this->isMultiinput) $form .= '</td>';
		return $form;
	}
}
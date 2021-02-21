<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/text.php';
class lib_pea_frm_datetimeinterval extends lib_pea_frm_text
{	
	public $dateFormat = 'd M Y H:i:s';
	public $dateConfig = array(
		'showDropdowns' => true,
		'timePicker'    => true,
		'locale'        => array(
			'format' => 'DD MMM YYYY HH:mm:ss',
		),
	);

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->setIncludes('daterangepicker/daterangepicker.min', 'css');
		$this->setIncludes('daterangepicker/moment.min', 'js');
		$this->setIncludes('daterangepicker/daterangepicker.min', 'js');
		$this->setIncludes('daterangepicker', 'js');
	}

	public function setDateConfig($index = '', $value = '')
	{
		if ($index) $this->dateConfig[$index] = $value;
	}

	public function setDateFormatInput($dateFormat = 'DD MMM YYYY HH:mm:ss')
	{
		$this->dateConfig['locale']['format'] = $dateFormat;
	}

	public function setDateFormat($dateFormat = 'd M Y H:i:s')
	{
		$this->dateFormat = $dateFormat;
	}

	public function getDateValue($index = '')
	{
		return date($this->dateFormat, strtotime($this->getValue($index)));
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
			// $name = ($this->isMultiform) ? $name.'[]' : $name;
			$id   = (is_numeric($index)) ? $this->name.'__'.$index : $this->name;
			$form .= '<input type="hidden" name="'.$name.'" value="'.$this->getValue($index).'">';
			$form .= '<input type="hidden" name="'.$name.'" value="'.$this->getValue($index).'">';
			$form .= '<input id="'.$id.'" type="text" class="form-control fm_daterangepicker '.$this->attr_class.'" value="" title="'.$this->caption.'" placeholder="'.$this->caption.'" '.$this->attr.' '.$this->isRequire.' data-config=\''.json_encode($this->dateConfig).'\'>';
		}
		if ($this->tips) $form .= '<div class="'.lib_bsv('help-block', 'form-text text-muted').'">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		$form .= $this->formAfter;
		if ($this->init == 'roll' and !$this->isMultiinput) $form .= '</td>';
		return $form;
	}
}
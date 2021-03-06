<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/text.php';
class lib_pea_frm_multiinput extends lib_pea_frm_text
{	
	public $delimiter     = '';
	public $delimiter_alt = '';

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->setPlainText();
		$this->setFieldName();
		$this->setType('multiinput');

		$this->element = new stdClass();
	}

	public function setDelimiter($delimiter = '')
	{
		$this->delimiter     = $delimiter;
		$this->delimiter_alt = $delimiter;
	}

	public function setDelimiterAlt($delimiter_alt = '')
	{
		$this->delimiter_alt = $delimiter_alt;
	}

	public function addInput($name, $type)
	{
		if (is_file(__DIR__.'/'.$type.'.php')) {
			include_once __DIR__.'/'.$type.'.php';
			eval('$this->element->$name = new lib_pea_frm_'.$type.'(array(
				\'table\'        => $this->table,
				\'table_id\'     => $this->table_id,
				\'where\'        => $this->where,
				\'db\'           => $this->db,
				\'init\'         => $this->init,
				\'_url\'         => $this->_url,
				\'_root\'        => $this->_root,
				\'isMultiinput\' => 1,
			), $name);');
			$this->element->$name->setInputPosition($this->fieldNameDb);
			$this->element->$name->setFailMsgTpl($this->failMsgTpl);
			$this->element->$name->setTitle(ucwords($name));
			foreach ($this->failMsg as $key => $value) {
				$this->element->$name->setFailMsg($value, $key);
			} 
		}else die('PEA::FORM "'.$type.'" tidak tersedia');
	}

	public function getReportOutput($value_ = [], $type = '', $id = 0, $index = '', $values = array())
	{
		$out = [];
		foreach ($this->element as $key => $value) {
			if (isset($value_[$key])) {
				$v = $value->getReportOutput($value_[$key], $type, $index, $values);
				if ($value->displayReportFunction) {
					$v = call_user_func($value->displayReportFunction, $v, $id, $index, $values);
				}
				$out[] = $v;
			}
		}
		return implode((in_array($type, ['excel','json'])) ? $this->delimiter_alt : $this->delimiter, $out);
	}

	public function getForm($index = '', $values = array())
	{
		$form = '';
		if ($this->init == 'roll') $form .= '<td>';
		$form .= $this->formBefore;
		if ($this->init != 'roll') $form .= '<div class="form-group">';
		if (in_array($this->init, ['edit','add'])) $form .= '<label>'.$this->title.'</label>';
		$form .= '<div class="form-inline '.$this->attr_class.'" '.$this->attr.'>';
		$forms = array();
		foreach ($this->element as $key => $value) {
			$forms[] = $value->getForm($index, $values);
		}
		$form .= implode($this->delimiter, $forms);
		$form .= '</div>';
		if ($this->tips) $form .= '<div class="'.lib_bsv('help-block', 'form-text text-muted').'">'.$this->tips.'</div>';
		if ($this->init != 'roll') $form .= '</div>';
		$form .= $this->formAfter;
		if ($this->init == 'roll') $form .= '</td>';
		return $form;
	}
}
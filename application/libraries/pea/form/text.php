<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class lib_pea_frm_text
{
	public $table               = '';
	public $table_id            = '';
	public $table_id_value      = '';
	public $table_id_value_roll = array();
	public $where               = '';
	public $db                  = '';
	public $init                = '';

	public $isMultiform = 0;

	public $title           = '';
	public $caption         = '';
	public $name            = '';
	public $fieldName       = '';
	public $value           = '';
	public $value_roll      = array();
	public $defaultValue    = '';
	public $type            = 'text';
	public $isRequire       = '';
	public $tips            = '';
	public $isPlainText     = 0;
	public $displayFunction = '';
	public $msg             = '';
	public $failMsg         = array();
	public $failMsgTpl      = '';
	public $inputPosition   = 'main';
	
	function __construct($opt, $name)
	{
		$this->table    = $opt['table'];
		$this->table_id = $opt['table_id'];
		$this->where    = $opt['where'];
		$this->db       = $opt['db'];
		$this->init     = $opt['init'];

		$this->isMultiform = isset($opt['isMultiform']) ? 1 : 0;
		
		$this->title     = ucwords($name);
		$this->caption   = ucwords($name);
		$this->name      = $this->table.'_'.$this->init.'_'.$name;
		$this->fieldName = $name;
	}

	public function setTitle($title = '')
	{
		$this->title = $title;
		if ($title) $this->setCaption($title);
	}

	public function setCaption($caption = '')
	{
		$this->caption = $caption;
	}

	public function setFieldName($fieldName = '')
	{
		$this->fieldName = $fieldName;
	}

	public function getFieldName()
	{
		return $this->fieldName;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setDefaultValue($defaultValue = '')
	{
		$this->defaultValue = $defaultValue;
	}

	public function setValue($value = '', $index = '')
	{
		if (is_numeric($index)) $this->value_roll[$index] = $value;
		else $this->value = $value;
	}

	public function getValue($index = '')
	{
		$value = (is_numeric($index)) ? @$this->value_roll[$index] : $this->value;
		if (!$value and $this->init == 'add') return $this->defaultValue;
		return $value;
	}

	public function setValueID($value = '', $index = '')
	{
		if (is_numeric($index)) $this->table_id_value_roll[$index] = $value;
		else $this->table_id_value = $value;
	}

	public function getValueID($index = '')
	{
		return (is_numeric($index)) ? @$this->table_id_value_roll[$index] : $this->table_id_value;
	}

	public function getPostValue($index = '')
	{
		$value = (is_numeric($index)) ? @$_POST[$this->getName()][$index] : @$_POST[$this->getName()];
		if ($this->getRequire()) {
			if (!$value) {
				$this->msg = str_replace('{msg}', str_replace('{title}', $this->title, @$this->failMsg['require']), $this->failMsgTpl);
			}
		}
		return $value;
	}

	public function setType($type = '')
	{
		$this->type = $type;
	}

	public function setRequire($isRequire = 1)
	{
		$this->isRequire = ($isRequire) ? 'required="required"' : '';
	}

	public function getRequire()
	{
		return ($this->isRequire) ? 1 : 0;
	}

	public function setInputPosition($inputPosition = '')
	{
		$this->inputPosition = ($inputPosition) ? $inputPosition : 'main';
	}

	public function getInputPosition()
	{
		return $this->inputPosition;
	}

	public function setFailMsg($failMsg = '', $index = '')
	{
		if ($failMsg and $index) $this->failMsg[$index] = $failMsg;
	}

	public function setFailMsgTpl($failMsgTpl = '')
	{
		if ($failMsgTpl) $this->failMsgTpl = $failMsgTpl;
	}

	public function getFailMsg()
	{
		return $this->msg;
	}

	public function setDisplayFunction($displayFunction = '')
	{
		if ($displayFunction) {
			if (function_exists($displayFunction)) {
				$this->displayFunction = $displayFunction;
			}
		}
	}

	public function setPlainText($isPlainText = 1)
	{
		$this->isPlainText = ($isPlainText) ? 1 : 0;
	}

	public function getPlainText()
	{
		return $this->isPlainText;
	}

	public function addTip($tips = '')
	{
		$this->tips = $tips;
	}

	public function getForm($index = '')
	{
		$form = '';
		if ($this->init == 'roll') $form .= '<td>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and $this->init != 'roll') $form .= '<label>'.$this->title.'</label>';
		if ($this->isPlainText) {
			$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getValue($index)) : $this->getValue($index);
			$form .= ($this->init == 'roll') ? $value : '<p>'.$value.'</p>';
		}else{
			$name = (is_numeric($index)) ? $this->name.'['.$index.']' : $this->name;
			$name = ($this->isMultiform) ? $name.'[]' : $name;
			$form .= '<input type="'.$this->type.'" name="'.$name.'" class="form-control" value="'.$this->getValue($index).'" title="'.$this->caption.'" placeholder="'.$this->caption.'" '.$this->isRequire.'>';
		}
		if ($this->tips) $form .= '<div class="help-block">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		if ($this->init == 'roll') $form .= '</td>';
		return $form;
	}
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class lib_pea_frm_text
{
	public $table    = '';
	public $table_id = '';
	public $where    = '';
	public $db       = '';
	public $init     = '';

	public $isMultiform = 0;

	public $title           = '';
	public $caption         = '';
	public $name            = '';
	public $fieldName       = '';
	public $value           = '';
	public $defaultValue    = '';
	public $type            = 'text';
	public $isRequire       = '';
	public $tips            = '';
	public $isPlainText     = 0;
	public $displayFunction = '';
	public $msg             = '';
	public $failMsg         = '
<div class="alert alert-danger" role="alert">
	<i class="fa fa-times"></i> <b>{title}</b> Must not empty !
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>';
	
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

	public function setValue($value = '')
	{
		$this->value = $value;
	}

	public function getValue()
	{
		if (!$this->value and $this->init == 'add') return $this->defaultValue;
		return $this->value;
	}

	public function setDefaultValue($defaultValue = '')
	{
		$this->defaultValue = $defaultValue;
	}

	public function getPostValue()
	{
		$value = @$_POST[$this->getName()];
		if ($this->getRequire()) {
			if (!$value) {
				$this->msg = str_replace('{title}', $this->title, $this->failMsg);
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

	public function setFailMsg($failMsg = '')
	{
		if ($failMsg) $this->failMsg = $failMsg;
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

	public function getForm()
	{
		$form = '';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and $this->init != 'roll') $form .= '<label>'.$this->title.'</label>';
		if ($this->isPlainText) {
			$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getValue()) : $this->getValue();
			$form .= '<p>'.$value.'</p>';
		}else{
			$name = ($this->isMultiform) ? $this->name.'[]' : $this->name;
			$form .= '<input type="'.$this->type.'" name="'.$name.'" class="form-control" value="'.$this->getValue().'" title="'.$this->caption.'" placeholder="'.$this->caption.'" '.$this->isRequire.'>';
		}
		if ($this->tips) $form .= '<div class="help-block">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		return $form;
	}
}
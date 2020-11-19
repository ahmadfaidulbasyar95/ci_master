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
	public $_url                = '';
	public $_root               = '';

	public $isMultiinput = 0;
	public $isMultiform  = 0;
	public $includes_js  = array();
	public $includes_css = array();

	public $title             = '';
	public $caption           = '';
	public $name              = '';
	public $fieldName         = '';
	public $fieldNameDb       = '';
	public $value             = '';
	public $value_roll        = array();
	public $defaultValue      = '';
	public $type              = 'text';
	public $isRequire         = '';
	public $tips              = '';
	public $isPlainText       = 0;
	public $displayFunction   = '';
	public $displayColumn     = 1;
	public $displayColumnTool = 0;
	public $msg               = '';
	public $failMsg           = array();
	public $failMsgTpl        = '';
	public $inputPosition     = 'main';
	public $isUniq            = 0;
	public $attr              = '';
	
	function __construct($opt, $name)
	{
		$this->table    = $opt['table'];
		$this->table_id = $opt['table_id'];
		$this->where    = $opt['where'];
		$this->db       = $opt['db'];
		$this->init     = $opt['init'];
		$this->_url     = $opt['_url'];
		$this->_root    = $opt['_root'];

		$this->isMultiinput = isset($opt['isMultiinput']) ? 1 : 0;
		$this->isMultiform  = isset($opt['isMultiform']) ? 1 : 0;
		
		$this->title       = ucwords($name);
		$this->caption     = ucwords($name);
		$this->name        = $this->table.'_'.$this->init.'_'.$name;
		$this->fieldName   = $name;
		$this->fieldNameDb = $name;
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
		$isUniq = $this->getUniq();
		if ($isUniq and !$this->msg) {
			$vId = $this->getValueID($index);
			$q   = 'SELECT 1 FROM '.$this->table.' WHERE `'.$this->fieldNameDb.'` = "'.addslashes($value).'"';
			if ($vId) {
				$q .= ' AND `'.$this->table_id.'` != "'.addslashes($vId).'"';
			}
			if (!is_numeric($isUniq)) {
				$q .= ' AND '.$isUniq;
			}
			if ($this->db->getOne($q)) {
				$this->msg = str_replace('{msg}', str_replace('{title}', $this->title, @$this->failMsg['uniq']), $this->failMsgTpl);
			}
		}
		return $value;
	}

	public function setType($type = '')
	{
		$this->type = $type;
	}

	public function setAttr($attr = '')
	{
		$this->attr = $attr;
	}

	public function setRequire($isRequire = 1)
	{
		$this->isRequire = ($isRequire) ? 'required="required"' : '';
	}

	public function getRequire()
	{
		return ($this->isRequire) ? 1 : 0;
	}

	public function setUniq($isUniq = 1)
	{
		$this->isUniq = ($isUniq) ? $isUniq : 0;
	}

	public function getUniq()
	{
		return $this->isUniq;
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

	public function setDisplayColumn($displayColumn = 1)
	{
		$this->displayColumn     = ($displayColumn) ? 1 : 0;
		$this->displayColumnTool = 1;
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

	public function setIncludes($file = '', $type = '')
	{
		if ($file) {
			switch ($type) {
				case 'js':
					$this->includes_js[] = $file;
					break;
				case 'css':
					$this->includes_css[] = $file;
					break;
			}
		}
	}

	public function getIncludes()
	{
		return array(
			'js'  => $this->includes_js,
			'css' => $this->includes_css,
		);
	}

	public function onSaveSuccess($index = '')
	{
		
	}

	public function onSaveFailed($index = '')
	{
		
	}

	public function onDeleteSuccess($index = '')
	{
		
	}

	public function getReportOutput($value = '')
	{
		return $value;
	}

	public function getRollTitle($sortConfig = array(), $active = '', $is_desc = '')
	{
		$link = $sortConfig['base_url'];
		$link = preg_replace('~'.$sortConfig['get_name'].'=[a-zA-Z0-9_]+&?~', '', $link);
		$link = preg_replace('~'.$sortConfig['get_name'].'_desc=[a-zA-Z0-9]+&?~', '', $link);
		$link .= (preg_match('/\?/', $link)) ? (preg_match('~[\?|&]$~', $link)) ? '' : '&' : '?';
		$link .= $sortConfig['get_name'].'='.urlencode($this->fieldNameDb);
		$link .= (($active == $this->fieldNameDb and $is_desc) or $active != $this->fieldNameDb) ? '' : '&'.$sortConfig['get_name'].'_desc=1';
		$title = $this->title;
		$title .= ($active == $this->fieldNameDb) ? ($is_desc) ? ' <i class="fa fa-sort-alpha-desc"></i>' : ' <i class="fa fa-sort-alpha-asc"></i>' : '' ;
		return '<a href="'.$link.'">'.$title.'</a>';
	}

	public function getSearchSql()
	{
		$value = $this->getValue();
		return (!$value and $value != '0') ? '' : '`'.$this->fieldNameDb.'` = "'.addslashes($value).'"';
	}

	public function getForm($index = '')
	{
		$form = '';
		if ($this->init == 'roll') $form .= '<td>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '<div class="form-group">';
		if (!$this->isMultiform and !$this->isMultiinput and in_array($this->init, ['edit','add'])) $form .= '<label>'.$this->title.'</label>';
		if ($this->isPlainText) {
			$value = ($this->displayFunction) ? call_user_func($this->displayFunction, $this->getValue($index)) : $this->getValue($index);
			$form .= ($this->init == 'roll' or $this->isMultiinput) ? $value : '<p>'.$value.'</p>';
		}else{
			$name = (is_numeric($index)) ? $this->name.'['.$index.']' : $this->name;
			$name = ($this->isMultiform) ? $name.'[]' : $name;
			$form .= '<input type="'.$this->type.'" name="'.$name.'" class="form-control" value="'.$this->getValue($index).'" title="'.$this->caption.'" placeholder="'.$this->caption.'" '.$this->attr.' '.$this->isRequire.'>';
		}
		if ($this->tips) $form .= '<div class="'.lib_bsv('help-block', 'form-text text-muted').'">'.$this->tips.'</div>';
		if (!$this->isPlainText or $this->init != 'roll') $form .= '</div>';
		if ($this->init == 'roll') $form .= '</td>';
		return $form;
	}
}
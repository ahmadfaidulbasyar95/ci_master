<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once dirname(__FILE__).'/text.php';
class lib_pea_frm_params extends lib_pea_frm_text
{	

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);

		$this->element = new stdClass();
	}

	public function setFailMsg($failMsg = '', $index = '')
	{
		foreach ($this->element as $value) {
			$value->setFailMsg($failMsg, $index);
		}
	}

	public function setIncludes($file = '', $type = '')
	{
		foreach ($this->element as $value) {
			$value->setIncludes($file, $type);
		}
	}

	public function getIncludes()
	{
		$includes_js  = array();
		$includes_css = array();
		foreach ($this->element as $value) {
			$includes = $value->getIncludes();
			foreach ($includes['js'] as $value1) {
				$includes_js[] = $value1;
			}
			foreach ($includes['css'] as $value1) {
				$includes_css[] = $value1;
			}
		}
		return array(
			'js'  => $includes_js,
			'css' => $includes_css,
		);
	}

	public function onSaveSuccess($index = '')
	{
		foreach ($this->element as $value) {
			$value->onSaveSuccess();
		}		
	}

	public function onSaveFailed($index = '')
	{
		foreach ($this->element as $value) {
			$value->onSaveFailed();
		}		
	}

	public function onDeleteSuccess($index = '')
	{
		foreach ($this->element as $value) {
			$value->onDeleteSuccess();
		}		
	}

	public function setValue($value = '', $index = '')
	{
		$values = json_decode($value, 1);
		foreach ($this->element as $key => $value) {
			$value->setValue(@$values[$key]);
		}		
	}

	public function setValueID($value = '', $index = '')
	{
		$values = json_decode($value, 1);
		foreach ($this->element as $key => $value) {
			$value->setValueID(@$values[$key]);
		}
	}

	public function getValue($index = '')
	{
		$values  = array();
		foreach ($this->element as $key => $value) {
			$values[$key] = $value->getValue();
		}		
		return json_encode($values);
	}

	public function getPostValue($index = '')
	{
		$select = array();
		foreach ($this->element as $key => $value) {
			if ($value->getFieldName()) {
				$select[$key] = $value->getFieldName();
				if ($key != $select[$key]) $select[$key] .= ' AS `'.$key.'`';
			}
		}
		$values  = array();
		foreach ($select as $key => $value) {
			if (!$this->element->$key->getPlainText()) {
				$values[$key] = $this->element->$key->getPostValue();
				$failMsg      = $this->element->$key->getFailMsg();
				if ($failMsg) {
					$this->msg .= $failMsg;
				}
			} 
		}
		return json_encode($values);
	}

	public function addInput($name, $type)
	{
		if (is_file(dirname(__FILE__).'/'.$type.'.php')) {
			include_once dirname(__FILE__).'/'.$type.'.php';
			eval('$this->element->$name = new lib_pea_frm_'.$type.'(array(
				\'table\'    => $this->table,
				\'table_id\' => $this->table_id,
				\'where\'    => $this->where,
				\'db\'       => $this->db,
				\'init\'     => $this->init,
				\'_url\'     => $this->_url,
				\'_root\'    => $this->_root,
			), $this->fieldNameDb.\'_\'.$name);');
			$this->element->$name->setFailMsgTpl($this->failMsgTpl);
			$this->element->$name->setTitle(ucwords($name));
			foreach ($this->failMsg as $key => $value) {
				$this->element->$name->setFailMsg($value, $key);
			} 
		}else die('PEA::FORM "'.$type.'" tidak tersedia');
	}

	public function getForm($index = '')
	{
		$form = '';
		foreach ($this->element as $key => $value) {
			$form .= $value->getForm();
		}
		return $form;
	}
}
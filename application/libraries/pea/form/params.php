<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/text.php';
class lib_pea_frm_params extends lib_pea_frm_text
{	
	public $showParams      = 0;
	public $showParams_load = 0;

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);

		$this->element = new stdClass();
	}

	public function getIncludes()
	{
		foreach ($this->element as $key => $value) {
			if ($value->type == 'multiinput') {
				foreach ($value->element as $key1 => $value1) {
					$this->element->$key1 = $value1;
				}
			}
		}
		$includes_js  = $this->includes_js;
		$includes_css = $this->includes_css;
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

	public function onSaveSuccess($index = '', $id = 0)
	{
		foreach ($this->element as $value) {
			$value->onSaveSuccess($index, $id);
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
		parent::setValue($values, $index);
		foreach ($this->element as $key => $value) {
			$value->setValue(@$values[$key], $index);
		}		
	}

	public function setValueID($value_ = '', $index = '')
	{
		parent::setValueID($value_, $index);
		foreach ($this->element as $key => $value) {
			$value->setValueID($value_, $index);
		}
	}

	public function getValue($index = '')
	{
		$values  = array();
		foreach ($this->element as $key => $value) {
			$values[$key] = $value->getValue($index);
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
				$values[$key] = $this->element->$key->getPostValue($index);
				$failMsg      = $this->element->$key->getFailMsg();
				if ($failMsg) {
					$this->msg .= $failMsg;
				}
			}else{
				$values[$key] = $this->element->$key->getValue($index);
			} 
		}
		return json_encode($values);
	}

	function setParams($fields = array())
	{
		foreach ($fields as $key => $value) {
			$v_name = $value['name'];
			$this->addInput($v_name, $value['form']);
			$this->element->$v_name->setTitle($value['title']);
			if ($value['required']) {
				$this->element->$v_name->setRequire();
			}
			$v_params = @(array)json_decode($value['params'], 1);
			if ($value['form'] == 'file') {
				$v_params_folder = 0;
				foreach ($v_params as $key1 => $value1) {
					if ($value1['method'] == 'setFolder') {
						$v_params_folder = 1;
						if (strpos($v_params[$key1]['args'][0] ,'files/params/'.$this->table.'/0/') === false) {
							$v_params[$key1]['args'][0] = 'files/params/'.$this->table.'/0/'.$v_params[$key1]['args'][0];
						}
					}
				}
				if (!$v_params_folder) {
					$v_params[] = array(
						'method' => 'setFolder',
						'args'   => ['files/params/'.$this->table.'/0/'],
					);
				}
				$fields[$key]['params'] = json_encode($v_params);
			}
			foreach ($v_params as $key1 => $value1) {
				call_user_func_array(array($this->element->$v_name, $value1['method']), $value1['args']);
			}
		}

		$this->addInput('element', 'extrafield');
		$this->element->element->setDefaultValue($fields);
	}

	function showParams($showParams = 1)
	{
		$this->showParams = ($showParams) ? 1 : 0;
	}

	public function addInput($name, $type)
	{
		if (is_file(__DIR__.'/'.$type.'.php')) {
			include_once __DIR__.'/'.$type.'.php';
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

	public function getForm($index = '', $values = array())
	{
		if ($this->title and in_array($this->init, ['add','edit'])) {
			$this->formWrap(lib_bsv('<div class="panel panel-default '.$this->attr_class.'" '.$this->attr.'><div class="panel-heading">'.$this->title.'</div><div class="panel-body">', '<div class="card '.$this->attr_class.'" '.$this->attr.'><div class="card-header">'.$this->title.'</div><div class="card-body">'),'</div></div>');
		}
		if ($this->init == 'roll') {
			$this->formWrap('<td><table><tbody><tr>','</tr></tbody></table></td>');
		}
		$form = $this->formBefore;
		if ($this->showParams and !$this->showParams_load) {
			$this->showParams_load = 1;
			$params_values         = parent::getValue($index);
			if (isset($params_values['element'][0]['name'])) {
				$this->setParams($params_values['element']);
				$params_valueID = parent::getValueID($index);
				foreach ($params_values['element'] as $value) {
					$v_name = $value['name'];
					$this->element->$v_name->setValue(@$params_values[$v_name], $index);
					$this->element->$v_name->setValueID($params_valueID, $index);
					$this->element->$v_name->setPlainText();
				}
			}
		}
		foreach ($this->element as $key => $value) {
			if ($value->getInputPosition() == 'main') $form .= $value->getForm($index, $values);
		}
		$form .= $this->formAfter;
		return $form;
	}
}
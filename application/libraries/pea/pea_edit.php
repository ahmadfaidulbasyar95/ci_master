<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class lib_pea_edit
{
	public $table    = '';
	public $table_id = '';
	public $where    = '';
	public $db       = '';
	public $init     = '';
	public $_url     = '';
	public $_root    = '';

	public $returnUrl                  = '';
	public $editValues                 = array();
	public $form                       = '';
	public $formBefore                 = '';
	public $formAfter                  = '';
	public $formHeader                 = '';
	public $formHeaderBefore           = '';
	public $formHeaderAfter            = '';
	public $formBodyBefore             = '';
	public $formBodyAfter              = '';
	public $formFooterBefore           = '';
	public $formFooterAfter            = '';
	public $do_action                  = 0;
	public $insertID                   = 0;
	public $saveTool                   = 1;
	public $saveButtonText             = '';
	public $saveButtonClass            = 'btn btn-primary';
	public $saveButtonName             = 'submit';
	public $deleteTool                 = 0;
	public $deleteButtonText           = '<i class="fa fa-trash"></i> Delete';
	public $deleteButtonClass          = 'btn btn-danger';
	public $onSaveReloadParentScript   = '';
	public $onDeleteReloadParentScript = '';
	public $onSaveFunction             = '';
	public $onDeleteFunction           = '';
	public $msg                        = '';
	public $successMsg                 = '';
	public $successDeleteMsg           = 'Success Delete Data';
	public $failMsg                    = array(
		'require' => '<b>{title}</b> Must not empty',
		'uniq'    => '<b>{title}</b> Already used',
	);
	public $successMsgTpl    = '
<div class="alert alert-success" role="alert">
	<i class="fa fa-check"></i> {msg} !
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>';
	public $failMsgTpl = '
<div class="alert alert-danger" role="alert">
	<i class="fa fa-times"></i> {msg} !
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>';

	function __construct($opt)
	{
		$this->table    = $opt['table'];
		$this->table_id = $opt['table_id'];
		$this->where    = $opt['where'];
		$this->db       = $opt['db'];
		$this->init     = $opt['init'];
		$this->_url     = $opt['_url'];
		$this->_root    = $opt['_root'];

		$this->input = new stdClass();
		
		$this->setSaveButton(($this->where) ? '<i class="fa fa-save"></i> Save' : '<i class="fa fa-plus"></i> Add'); 
		$this->setSuccessMsg(($this->where) ? 'Success Save Data' : 'Success Insert New Data'); 

		if (isset($_GET['return'])) $this->returnUrl = $_GET['return'];
		
		$this->returnTool        = 1;
		$this->returnButtonText  = '<i class="fa fa-chevron-left"></i>&nbsp;';
		$this->returnButtonClass = lib_bsv('btn btn-default', 'btn btn-secondary');
	}

	public function setSaveTool($saveTool = 1)
	{
		$this->saveTool = ($saveTool) ? 1 : 0;
	}

	public function setSaveButton($text = '', $class = '', $name = '')
	{
		if ($text) $this->saveButtonText   = $text;
		if ($class) $this->saveButtonClass = $class;
		if ($name) $this->saveButtonName   = $name;
	}

	public function setReturnTool($returnTool = 1)
	{
		$this->returnTool = ($returnTool) ? 1 : 0;
	}

	public function setReturnButton($text = '', $class = '')
	{
		if ($text) $this->returnButtonText   = $text;
		if ($class) $this->returnButtonClass = $class;
	}

	public function setDeleteTool($deleteTool = 1)
	{
		$this->deleteTool = ($deleteTool) ? 1 : 0;
	}

	public function setDeleteButton($text = '', $class = '')
	{
		if ($text) $this->deleteButtonText   = $text;
		if ($class) $this->deleteButtonClass = $class;
	}

	public function setSuccessDeleteMsg($successDeleteMsg = '')
	{
		if ($successDeleteMsg) $this->successDeleteMsg = $successDeleteMsg;
	}

	public function setSuccessMsg($successMsg = '')
	{
		if ($successMsg) $this->successMsg = $successMsg;
	}

	public function setSuccessMsgTpl($successMsgTpl = '')
	{
		if ($successMsgTpl) $this->successMsgTpl = $successMsgTpl;
	}

	public function setFailMsg($failMsg = '', $index = '')
	{
		if ($failMsg and $index) {
			$this->failMsg[$index] = $failMsg;
			foreach ($this->input as $value) {
				$value->setFailMsg($failMsg, $index);
			}
		}
	}

	public function setFailMsgTpl($failMsgTpl = '')
	{
		if ($failMsgTpl) $this->failMsgTpl = $failMsgTpl;
	}

	public function setHeader($header = '')
	{
		$this->formHeader = $header;
		$this->formWrap(lib_bsv('<div class="panel panel-default">', '<div class="card">'),'</div>');
		$this->headerWrap(lib_bsv('<div class="panel-heading">', '<div class="card-header">'),'</div>');
		$this->bodyWrap(lib_bsv('<div class="panel-body">', '<div class="card-body">'),'</div>');
		$this->footerWrap(lib_bsv('<div class="panel-footer">', '<div class="card-footer">'),'</div>');
	}

	public function formWrap($before = '', $after = '')
	{
		$this->formBefore = $before;
		$this->formAfter  = $after;
	}

	public function headerWrap($before = '', $after = '')
	{
		$this->formHeaderBefore = $before;
		$this->formHeaderAfter  = $after;
	}

	public function bodyWrap($before = '', $after = '')
	{
		$this->formBodyBefore = $before;
		$this->formBodyAfter  = $after;
	}

	public function footerWrap($before = '', $after = '')
	{
		$this->formFooterBefore = $before;
		$this->formFooterAfter  = $after;
	}

	public function setIncludes($data = array())
	{
		foreach (@(array)$data['js'] as $value) {
			if (!in_array($value, @(array)$GLOBALS['pea_includes']['js'])) $GLOBALS['pea_includes']['js'][] = $value;
		}
		foreach (@(array)$data['css'] as $value) {
			if (!in_array($value, @(array)$GLOBALS['pea_includes']['css'])) $GLOBALS['pea_includes']['css'][] = $value;
		}
	}

	public function getIncludes()
	{
		$out = '';
		foreach (@(array)$GLOBALS['pea_includes']['js'] as $key => $value) {
			if (!in_array($value, @(array)$GLOBALS['pea_includes_load']['js'])) {
				$GLOBALS['pea_includes_load']['js'][] = $value;
				$out .= '<script src="'.$this->_url.'application/libraries/pea/includes/'.$value.'.js"></script>';
			}
		}
		foreach (@(array)$GLOBALS['pea_includes']['css'] as $key => $value) {
			if (!in_array($value, @(array)$GLOBALS['pea_includes_load']['css'])) {
				$GLOBALS['pea_includes_load']['css'][] = $value;
				$out .= '<link rel="stylesheet" href="'.$this->_url.'application/libraries/pea/includes/'.$value.'.css">';
			}
		}
		return $out;
	}

	public function addInput($name, $type)
	{
		if (is_file(__DIR__.'/form/'.$type.'.php')) {
			include_once __DIR__.'/form/'.$type.'.php';
			eval('$this->input->$name = new lib_pea_frm_'.$type.'(array(
				\'table\'    => $this->table,
				\'table_id\' => $this->table_id,
				\'where\'    => $this->where,
				\'db\'       => $this->db,
				\'init\'     => $this->init,
				\'_url\'     => $this->_url,
				\'_root\'    => $this->_root,
			), $name);');
			$this->input->$name->setFailMsgTpl($this->failMsgTpl);
			foreach ($this->failMsg as $key => $value) {
				$this->input->$name->setFailMsg($value, $key);
			} 
		}else die('PEA::FORM "'.$type.'" tidak tersedia');
	}

	public function addExtraField($name, $value, $init = '')
	{
		if (!$init or ($init and $init == $this->init)) {
			$this->addInput($name, 'extrafield');
			$this->input->$name->setDefaultValue($value);
		}
	}

	public function onSaveReloadParent()
	{
		$this->onSaveReloadParentScript = '
<script type="text/javascript">
	(function() {
		window.addEventListener(\'load\', function() { 
			parent.location.href = parent.location.href;
		}, false);
	})();
</script>';
	}

	public function onDeleteReloadParent()
	{
		$this->onDeleteReloadParentScript = '
<script type="text/javascript">
	(function() {
		window.addEventListener(\'load\', function() { 
			parent.location.href = parent.location.href;
		}, false);
	})();
</script>';
	}

	public function onSave($onSaveFunction = '')
	{
		if ($onSaveFunction) {
			if (function_exists($onSaveFunction)) {
				$this->onSaveFunction = $onSaveFunction;
			}
		}
	}

	public function onDelete($onDeleteFunction = '')
	{
		if ($onDeleteFunction) {
			if (function_exists($onDeleteFunction)) {
				$this->onDeleteFunction = $onDeleteFunction;
			}
		}
	}

	public function action()
	{
		if (!$this->do_action) {
			$this->do_action = 1;
			if ($this->init == 'add') $this->setDeleteTool(0);
			foreach ($this->input as $key => $value) {
				if ($value->type == 'multiinput') {
					foreach ($value->element as $key1 => $value1) {
						$this->input->$key1 = $value1;
					}
				}
			}
			$select = array();
			foreach ($this->input as $key => $value) {
				$this->setIncludes($value->getIncludes());
				if ($value->getFieldName()) {
					$select[$key] = $value->getFieldName();
					if ($key != $select[$key]) $select[$key] .= ' AS `'.$key.'`';
				}
			}
			if ($select) {
				if ($this->where) {
					$select['edit_id'] = $this->table_id.' AS `edit_id`';
					$this->editValues  = $this->db->getRow('SELECT '.implode(' , ', $select).' FROM '.$this->table.' '.$this->where);
					foreach ($this->input as $key => $value) {
						if (isset($this->editValues[$key])) {
							$value->setValue($this->editValues[$key]);
							$value->setValueID($this->editValues['edit_id']);
						}
					}
					unset($select['edit_id']);
				}
				if ($this->deleteTool and isset($_POST[$this->table.'_'.$this->init.'_delete']) and $this->where) {
					if ($this->onDeleteFunction and @$this->editValues['edit_id']) call_user_func($this->onDeleteFunction, @$this->editValues['edit_id'], $this);
					$this->db->delete($this->table, preg_replace('~^.*?[W|w][H|h][E|e][R|r][E|e]~', '', $this->where));
					$this->msg = str_replace('{msg}', $this->successDeleteMsg, $this->successMsgTpl).$this->onDeleteReloadParentScript;
					foreach ($select as $key => $value) {
						$this->input->$key->onDeleteSuccess();
					}
				}else{
					if ($this->saveTool and isset($_POST[$this->table.'_'.$this->init.'_'.$this->saveButtonName])) {
						$isValid = 1;
						$values  = array();
						foreach ($select as $key => $value) {
							if (!$this->input->$key->getPlainText()) {
								$values[$key] = $this->input->$key->getPostValue();
								$failMsg      = $this->input->$key->getFailMsg();
								if ($failMsg) {
									$isValid    = 0;
									$this->msg .= $failMsg;
								}
							} 
						}
						if ($isValid) {
							if ($this->where) {
								$this->db->update($this->table, $values, preg_replace('~^.*?[W|w][H|h][E|e][R|r][E|e]~', '', $this->where));
							}else{
								$this->editValues['edit_id'] = $this->db->insert($this->table, $values);
							}
							$this->msg = str_replace('{msg}', $this->successMsg, $this->successMsgTpl).$this->onSaveReloadParentScript;
							foreach ($select as $key => $value) {
								$this->input->$key->onSaveSuccess();
							}
							if ($this->onSaveFunction and @$this->editValues['edit_id']) call_user_func($this->onSaveFunction, @$this->editValues['edit_id'], $this);
						}else{
							foreach ($select as $key => $value) {
								$this->input->$key->onSaveFailed();
							}
						}
					}
					if ($this->where) {
						$select['edit_id'] = $this->table_id.' AS `edit_id`';
						$this->editValues  = $this->db->getRow('SELECT '.implode(' , ', $select).' FROM '.$this->table.' '.$this->where);
						foreach ($this->input as $key => $value) {
							if (isset($this->editValues[$key])) {
								$value->setValue($this->editValues[$key]);
								$value->setValueID($this->editValues['edit_id']);
							}
						}
					}
				}
			}
		}
	}

	public function getMsg($reset = 0)
	{
		$msg = $this->msg;
		if ($reset) $this->msg = '';
		return $msg;
	}

	public function getForm()
	{
		$this->action();
		$this->form = '<form class="form_pea_edit" autocomplete="off" method="POST" action="" enctype="multipart/form-data">';
			$this->form .= $this->formBefore;
				$this->form .= $this->formHeaderBefore;
					$this->form .= $this->formHeader;
				$this->form .= $this->formHeaderAfter;
				$this->form .= $this->formBodyBefore;
					$this->form .= $this->msg;
					if (!isset($_POST[$this->table.'_'.$this->init.'_delete'])) {
						foreach ($this->input as $value) {
							if ($value->getInputPosition() == 'main') $this->form .= $value->getForm();
						}
					}
				$this->form .= $this->formBodyAfter;
				if ($this->saveTool or $this->deleteTool or ($this->returnUrl and $this->returnTool)) $this->form .= $this->formFooterBefore;
					if ($this->returnUrl and $this->returnTool) $this->form .= '<a href="'.$this->returnUrl.'" class="'.$this->returnButtonClass.'">'.$this->returnButtonText.'</a>&nbsp;';
					if ($this->saveTool and !isset($_POST[$this->table.'_'.$this->init.'_delete'])) $this->form .= '<button type="submit" name="'.$this->table.'_'.$this->init.'_'.$this->saveButtonName.'" value="'.$this->init.'" class="'.$this->saveButtonClass.'">'.$this->saveButtonText.'</button>&nbsp;';
					if ($this->deleteTool and !isset($_POST[$this->table.'_'.$this->init.'_delete'])) $this->form .= '<button type="submit" name="'.$this->table.'_'.$this->init.'_delete" value="'.$this->init.'" class="'.$this->deleteButtonClass.'" onclick="return confirm(\''.strip_tags($this->deleteButtonText).' ?\')">'.$this->deleteButtonText.'</button>&nbsp;';
				if ($this->saveTool or $this->deleteTool or ($this->returnUrl and $this->returnTool)) $this->form .= $this->formFooterAfter;
			$this->form .= $this->formAfter;
		$this->form .= '</form>';
		$this->form .= $this->getIncludes();
		return $this->form;
	}
}
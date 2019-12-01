<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class lib_pea_edit
{
	public $table    = '';
	public $table_id = '';
	public $where    = '';
	public $db       = '';
	public $init     = '';

	public $form             = '';
	public $formBefore       = '';
	public $formAfter        = '';
	public $formHeader       = '';
	public $formHeaderBefore = '';
	public $formHeaderAfter  = '';
	public $formBodyBefore   = '';
	public $formBodyAfter    = '';
	public $formFooterBefore = '';
	public $formFooterAfter  = '';
	public $do_action        = 0;
	public $insertID         = 0;
	public $saveTool         = 1;
	public $saveButtonText   = '';
	public $saveButtonClass  = 'btn btn-primary';
	public $successMsg       = '';
	public $msg              = '';

	function __construct($opt)
	{
		$this->table    = $opt['table'];
		$this->table_id = $opt['table_id'];
		$this->where    = $opt['where'];
		$this->db       = $opt['db'];
		$this->init     = $opt['init'];

		$this->input          = new stdClass();
		$this->saveButtonText = ($this->where) ? '<i class="fa fa-save"></i> Save' : '<i class="fa fa-plus"></i> Add'; 
		$this->successMsg     = ($this->where) ? '
		<div class="alert alert-success" role="alert">
			<i class="fa fa-check"></i> Success Save Data !
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>' : '
		<div class="alert alert-success" role="alert">
			<i class="fa fa-check"></i> Success Insert New Data !
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>'; 
	}

	public function setSaveTool($saveTool = 1)
	{
		$this->saveTool = ($saveTool) ? 1 : 0;
	}

	public function setSaveButton($text = '', $class = '')
	{
		if ($text) $this->saveButtonText   = $text;
		if ($class) $this->saveButtonClass = $class;
	}

	public function setSuccessMsg($successMsg = '')
	{
		if ($successMsg) $this->successMsg = $successMsg;
	}

	public function setHeader($header = '')
	{
		$this->formHeader = $header;
		$this->formWrap('<div class="panel panel-default">','</div>');
		$this->headerWrap('<div class="panel-heading">','</div>');
		$this->bodyWrap('<div class="panel-body">','</div>');
		$this->footerWrap('<div class="panel-footer">','</div>');
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

	public function addInput($name, $type)
	{
		if (is_file(dirname(__FILE__).'/form/'.$type.'.php')) {
			include_once dirname(__FILE__).'/form/'.$type.'.php';
			eval('$this->input->$name = new lib_pea_frm_'.$type.'(array(
				\'table\'    => $this->table,
				\'table_id\' => $this->table_id,
				\'where\'    => $this->where,
				\'db\'       => $this->db,
				\'init\'     => $this->init,
			), $name);'); 
		}else die('PEA::FORM "'.$type.'" tidak tersedia');
	}

	public function action()
	{
		if (!$this->do_action) {
			$this->do_action = 1;
			if (isset($this->input)) {
				$select = array();
				foreach ($this->input as $key => $value) {
					if ($value->getFieldName()) {
						$select[$key] = $value->getFieldName();
						if ($key != $select[$key]) $select[$key] .= ' AS `'.$key.'`';
					}
				}
				if ($select) {
					if ($this->saveTool and isset($_POST[$this->table.'_'.$this->init.'_submit'])) {
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
								$this->insertID = $this->db->insert($this->table, $values);
							}
							$this->msg = $this->successMsg;
						}
					}
					if ($this->where) {
						$values = $this->db->getRow('SELECT '.implode(' , ', $select).' FROM '.$this->table.' '.$this->where);
						foreach ($this->input as $key => $value) {
							if (isset($values[$key])) $value->setValue($values[$key]);
						}
					}
				}
			}
		}
	}

	public function getForm()
	{
		$this->action();
		$this->form = '<form autocomplete="off" method="POST" action="" enctype="multipart/form-data">';
			$this->form .= $this->formBefore;
				$this->form .= $this->formHeaderBefore;
					$this->form .= $this->formHeader;
				$this->form .= $this->formHeaderAfter;
				$this->form .= $this->formBodyBefore;
					$this->form .= $this->msg;
					if (isset($this->input)) {
						foreach ($this->input as $value) {
							$this->form .= $value->getForm();
						}
					}
				$this->form .= $this->formBodyAfter;
				if ($this->saveTool) $this->form .= $this->formFooterBefore;
					if ($this->saveTool) $this->form .= '<button type="submit" name="'.$this->table.'_'.$this->init.'_submit'.'" value="'.$this->init.'" class="'.$this->saveButtonClass.'">'.$this->saveButtonText.'</button>';
				if ($this->saveTool) $this->form .= $this->formFooterAfter;
			$this->form .= $this->formAfter;
		$this->form .= '</form>';
		return $this->form;
	}
}
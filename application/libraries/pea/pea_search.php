<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once dirname(__FILE__).'/pea_edit.php';
class lib_pea_search extends lib_pea_edit
{
	public $values = array();
	public $sql    = array();

	function __construct($opt)
	{
		parent::__construct($opt);

		$this->setSaveButton('<i class="fa fa-search"></i>&nbsp;'); 
	}

	public function keyword()
	{
		return $this->values;
	}

	public function action()
	{
		if (!$this->do_action) {
			$this->do_action = 1;
			if (isset($_POST[$this->table.'_'.$this->init.'_submit'])) {
				$this->values = array();
				foreach ($this->input as $key => $value) {
					if (!$value->getPlainText()) {
						$this->values[$key] = $value->getPostValue();
					}
				}
				$_SESSION['pea_search'][$this->table] = $this->values;
			}
			$this->values = @(array)$_SESSION['pea_search'][$this->table];
			foreach ($this->input as $key => $value) {
				if (!$value->getPlainText()) {
					$value->setValue(@$this->values[$key]);
					$value_sql = $value->getSearchSql();
					if ($value_sql) $this->sql[] = $value_sql;
				}
			}
		}
		return ($this->sql) ? 'WHERE '.implode(' AND ', $this->sql) : 'WHERE 1';
	}

	public function getForm()
	{
		$this->action();
		$this->form = '<form class="form_pea_search form-inline" autocomplete="off" method="POST" action="" enctype="multipart/form-data">';
			$this->form .= $this->formBefore;
				$this->form .= $this->formHeaderBefore;
					$this->form .= $this->formHeader;
				$this->form .= $this->formHeaderAfter;
				$this->form .= $this->formBodyBefore;
					foreach ($this->input as $value) {
						if ($value->getInputPosition() == 'main') $this->form .= $value->getForm();
					}
				$this->form .= $this->formBodyAfter;
				if ($this->saveTool) $this->form .= $this->formFooterBefore;
					if ($this->saveTool) $this->form .= '&nbsp;<button type="submit" name="'.$this->table.'_'.$this->init.'_submit" value="'.$this->init.'" class="'.$this->saveButtonClass.'">'.$this->saveButtonText.'</button>';
				if ($this->saveTool) $this->form .= $this->formFooterAfter;
			$this->form .= $this->formAfter;
		$this->form .= '</form>';
		$this->form .= $this->getIncludes();
		return $this->form;
	}
}
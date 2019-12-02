<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once dirname(__FILE__).'/pea_edit.php';
class lib_pea_roll extends lib_pea_edit
{
	public $formTableBefore           = '';
	public $formTableAfter            = '';
	public $formTableHeaderBefore     = '';
	public $formTableHeaderAfter      = '';
	public $formTableBodyBefore       = '';
	public $formTableBodyAfter        = '';
	public $formTableFooterBefore     = '';
	public $formTableFooterAfter      = '';
	public $formTableItemHeaderBefore = '';
	public $formTableItemHeaderAfter  = '';
	public $formTableItemBodyBefore   = '';
	public $formTableItemBodyAfter    = '';
	public $formTableItemFooterBefore = '';
	public $formTableItemFooterAfter  = '';
	public $rollValues                = array();

	function __construct($opt)
	{
		parent::__construct($opt);

		$this->setSaveButton('<i class="fa fa-save"></i> Save'); 
		$this->setSuccessMsg('Success Save Data'); 
		
		$this->tableWrap('<table class="table table-striped table-bordered table-hover">','</table>');
		$this->tableHeaderWrap('<thead>','</thead>');
		$this->tableBodyWrap('<tbody>','</tbody>');
		$this->tableFooterWrap('<tfoot>','</tfoot>');
		$this->tableItemHeaderWrap('<tr>','</tr>');
		$this->tableItemBodyWrap('<tr>','</tr>');
		$this->tableItemFooterWrap('<tr>','</tr>');
	}

	public function tableWrap($before = '', $after = '')
	{
		$this->formTableBefore = $before;
		$this->formTableAfter  = $after;
	}

	public function tableHeaderWrap($before = '', $after = '')
	{
		$this->formTableHeaderBefore = $before;
		$this->formTableHeaderAfter  = $after;
	}

	public function tableBodyWrap($before = '', $after = '')
	{
		$this->formTableBodyBefore = $before;
		$this->formTableBodyAfter  = $after;
	}

	public function tableFooterWrap($before = '', $after = '')
	{
		$this->formTableFooterBefore = $before;
		$this->formTableFooterAfter  = $after;
	}

	public function tableItemHeaderWrap($before = '', $after = '')
	{
		$this->formTableItemHeaderBefore = $before;
		$this->formTableItemHeaderAfter  = $after;
	}

	public function tableItemBodyWrap($before = '', $after = '')
	{
		$this->formTableItemBodyBefore = $before;
		$this->formTableItemBodyAfter  = $after;
	}

	public function tableItemFooterWrap($before = '', $after = '')
	{
		$this->formTableItemFooterBefore = $before;
		$this->formTableItemFooterAfter  = $after;
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
					$select['roll_id'] = $this->table_id.' AS `roll_id`';					
					$this->rollValues = $this->db->getAll('SELECT '.implode(' , ', $select).' FROM '.$this->table.' '.$this->where);
					foreach ($this->rollValues as $key => $value) {
						foreach ($this->input as $key1 => $value1) {
							if (isset($value[$key1])) $value1->setValue($value[$key1], $value['roll_id']);
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
					$this->form .= $this->formTableBefore;
						$this->form .= $this->formTableHeaderBefore;
							$this->form .= $this->formTableItemHeaderBefore;
								if (isset($this->input)) {
									foreach ($this->input as $value1) {
										$this->form .= '<th>'.$value1->title.'</th>';
									}
								}
							$this->form .= $this->formTableItemHeaderBefore;
						$this->form .= $this->formTableHeaderAfter;
						$this->form .= $this->formTableBodyBefore;
							foreach ($this->rollValues as $value) {
								$this->form .= $this->formTableItemBodyBefore;
								if (isset($this->input)) {
									foreach ($this->input as $value1) {
										$this->form .= $value1->getForm($value['roll_id']);
									}
								}
								$this->form .= $this->formTableItemBodyAfter;
							}
						$this->form .= $this->formTableBodyAfter;
						$this->form .= $this->formTableFooterBefore;
							$this->form .= $this->formTableItemFooterBefore;
								if ($this->saveTool) $this->form .= '<td><button type="submit" name="'.$this->table.'_'.$this->init.'_submit'.'" value="'.$this->init.'" class="'.$this->saveButtonClass.'">'.$this->saveButtonText.'</button></td>';
							$this->form .= $this->formTableItemFooterBefore;
						$this->form .= $this->formTableFooterAfter;
					$this->form .= $this->formTableAfter;
				$this->form .= $this->formBodyAfter;
			$this->form .= $this->formAfter;
		$this->form .= '</form>';
		return $this->form;
	}
}
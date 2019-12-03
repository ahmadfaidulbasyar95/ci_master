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
	public $rollDeleteInput           = array();
	public $rollDeleteCondition       = array();
	public $rollColumn                = 0;

	function __construct($opt)
	{
		parent::__construct($opt);

		$this->setSaveButton('<i class="fa fa-save"></i> Save'); 
		$this->setSuccessMsg('Success Save Data'); 
		$this->setDeleteTool(1); 
		
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

	public function getRollDeleteInput($index = 0)
	{
		if (in_array($index, $this->rollDeleteInput)) {
			return '
<div class="checkbox">
	<label>
		<input type="checkbox" name="'.$this->table.'_'.$this->init.'_delete_item['.$index.']" value="1" title="'.strip_tags($this->deleteButtonText).'">
		'.strip_tags($this->deleteButtonText).'
	</label>
</div>';
		}else return '';
	}

	public function setRollDeleteCondition($condition = '') // if ({$condition}) -> use {} to get value of field
	{
		if ($condition) $this->rollDeleteCondition[] = $condition;
	}

	public function action()
	{
		if (!$this->do_action) {
			$this->do_action = 1;
			if (isset($this->input)) {
				$select = array();
				foreach ($this->input as $key => $value) {
					if ($value->getInputPosition() == 'main') $this->rollColumn += 1;
					if ($value->getFieldName()) {
						$select[$key] = $value->getFieldName();
						if ($key != $select[$key]) $select[$key] .= ' AS `'.$key.'`';
					}
				}
				if ($select) {
					if ($this->deleteTool and isset($_POST[$this->table.'_'.$this->init.'_delete'])) {
						foreach (@(array)$_POST[$this->table.'_'.$this->init.'_ids'] as $value) {
							if (isset($_POST[$this->table.'_'.$this->init.'_delete_item'][$value])) {
								$this->db->delete($this->table, [$this->table_id => $value]);
							}
						}
						$this->msg = str_replace('{msg}', $this->successDeleteMsg, $this->successMsgTpl);
					}
					if ($this->saveTool and isset($_POST[$this->table.'_'.$this->init.'_submit'])) {
						$isValid = 1;
						$values  = array();
						foreach (@(array)$_POST[$this->table.'_'.$this->init.'_ids'] as $value) {
							foreach ($select as $key => $value1) {
								if ($isValid) {
									if (!$this->input->$key->getPlainText()) {
										$values[$value][$key] = $this->input->$key->getPostValue($value);
										$failMsg              = $this->input->$key->getFailMsg();
										if ($failMsg) {
											$isValid    = 0;
											$this->msg .= $failMsg;
										}
									} 
								}
							}
						}
						if ($isValid) {
							foreach ($values as $key => $value) {
								$this->db->update($this->table, $value, [$this->table_id => $key]);
							}
							$this->msg = str_replace('{msg}', $this->successMsg, $this->successMsgTpl);
						}
					}
					$select['roll_id'] = $this->table_id.' AS `roll_id`';
					$this->rollValues = $this->db->getAll('SELECT '.implode(' , ', $select).' FROM '.$this->table.' '.$this->where);
					foreach ($this->rollValues as $value) {
						foreach ($this->input as $key1 => $value1) {
							if (isset($value[$key1])) {
								$value1->setValue($value[$key1], $value['roll_id']);
								$value1->setValueID($value['roll_id'], $value['roll_id']);
							}
						}
					}
					if ($this->deleteTool) {
						foreach ($this->rollValues as $value) {
							$value_delete = 1;
							foreach ($this->rollDeleteCondition as $value1) {
								foreach ($value as $key2 => $value2) {
									$value1 = str_replace('{'.$key2.'}', $value2, $value1);
								}
								eval('if ('.$value1.') $value_delete = 0;');
							}
							if ($value_delete) $this->rollDeleteInput[] = $value['roll_id'];
						}
					}
				}
			}
		}
	}

	public function getForm()
	{
		$this->action();
		$this->form = '<form class="form_pea_roll" autocomplete="off" method="POST" action="" enctype="multipart/form-data">';
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
										if ($value1->getInputPosition() == 'main') $this->form .= '<th>'.$value1->title.'</th>';
									}
								}
								if ($this->deleteTool) $this->form .= '<th>'.strip_tags($this->deleteButtonText).'</th>'; 
							$this->form .= $this->formTableItemHeaderBefore;
						$this->form .= $this->formTableHeaderAfter;
						$this->form .= $this->formTableBodyBefore;
							foreach ($this->rollValues as $value) {
								$this->form .= $this->formTableItemBodyBefore;
								if (isset($this->input)) {
									$this->form .= '<input type="hidden" name="'.$this->table.'_'.$this->init.'_ids[]" value="'.$value['roll_id'].'">';
									foreach ($this->input as $value1) {
										if ($value1->getInputPosition() == 'main') $this->form .= $value1->getForm($value['roll_id']);
									}
									if ($this->deleteTool) $this->form .= '<td>'.$this->getRollDeleteInput($value['roll_id']).'</td>'; 
								}
								$this->form .= $this->formTableItemBodyAfter;
							}
						$this->form .= $this->formTableBodyAfter;
						$this->form .= $this->formTableFooterBefore;
							$this->form .= $this->formTableItemFooterBefore;
								$this->form .= '<td colspan="'.$this->rollColumn.'">';
									if ($this->returnUrl and $this->returnTool) $this->form .= '<a href="'.$this->returnUrl.'" class="'.$this->returnButtonClass.'">'.$this->returnButtonText.'</a>&nbsp;';
									if ($this->saveTool) $this->form .= '<button type="submit" name="'.$this->table.'_'.$this->init.'_submit" value="'.$this->init.'" class="'.$this->saveButtonClass.'">'.$this->saveButtonText.'</button>';
								$this->form .= '</td>';
								if ($this->deleteTool) $this->form .= '<td><button type="submit" name="'.$this->table.'_'.$this->init.'_delete" value="'.$this->init.'" class="'.$this->deleteButtonClass.'" onclick="return confirm(\''.strip_tags($this->deleteButtonText).' ?\')">'.$this->deleteButtonText.'</button></td>';
							$this->form .= $this->formTableItemFooterBefore;
						$this->form .= $this->formTableFooterAfter;
					$this->form .= $this->formTableAfter;
				$this->form .= $this->formBodyAfter;
			$this->form .= $this->formAfter;
		$this->form .= '</form>';
		return $this->form;
	}
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once dirname(__FILE__).'/../pagination.php';
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
	public $rollFoundRows             = 0;
	public $sortConfig                = array(
		'get_name' => 'sort',
		'base_url' => '',
	);
	public $paginationConfig = array(
		'get_name'        => 'page',
		'base_url'        => '',
		'num_links'       => 10,
		'per_page'        => 15,
		'prev_msg'        => '<h5 class="pull-left">Result {from} - {to} from total {total}</h5>',
		'full_tag_open'   => '<ul class="pagination pagination-sm" style="margin:0;">',
		'first_tag_open'  => '<li>',
		'first_link'      => '&laquo;&laquo;',
		'first_tag_close' => '</li>',
		'prev_tag_open'   => '<li>',
		'prev_link'       => '&laquo;',
		'prev_tag_close'  => '</li>',
		'num_tag_open'    => '<li>',
		'num_tag_close'   => '</li>',
		'cur_tag_open'    => '<li class="active">',
		'cur_tag_close'   => '</li>',
		'next_tag_open'   => '<li>',
		'next_link'       => '&raquo;',
		'next_tag_close'  => '<li>',
		'last_tag_open'   => '<li>',
		'last_link'       => '&raquo;&raquo;',
		'last_tag_close'  => '</li>',
		'full_tag_close'  => '</ul>',
		'go_tag_open'     => '<ul class="pagination pagination-sm" style="margin:0;"><li>',
		'go_question'     => 'Go to page ? of {totalpage}',
		'go_link'         => 'Go to',
		'go_tag_close'    => '</li></ul>',
	);

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

		$this->setSortConfig('base_url', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		$this->setPaginationConfig('base_url', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	}

	public function setPaginationConfig($name = '', $value = '')
	{
		if ($name and $value and isset($this->paginationConfig[$name])) $this->paginationConfig[$name] = $value; 
	}

	public function getPagination()
	{
		return lib_pagination($this->rollFoundRows, intval($this->paginationConfig['per_page']), @intval($_GET[$this->paginationConfig['get_name']]), $this->paginationConfig['get_name'], $this->paginationConfig['base_url'], $this->paginationConfig['num_links'], 0 , $this->paginationConfig);
	}

	public function setSortConfig($name = '', $value = '')
	{
		if ($name and $value and isset($this->sortConfig[$name])) $this->sortConfig[$name] = $value; 
	}

	public function getSort()
	{
		$ret = $this->where;
		if (@$_GET[$this->sortConfig['get_name']]) {
			$ret = preg_replace('~\s[O|o][R|r][D|d][E|e][R|r]\s[B|b][Y|y]\s.*?$~', '', $ret);
			$ret .= ' ORDER BY '.addslashes(@$_GET[$this->sortConfig['get_name']]);
			if (@$_GET[$this->sortConfig['get_name'].'_desc']) $ret .= ' DESC'; 
		}
		return $ret;
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

	public function getRollDeleteTitle()
	{
		return '
<div class="checkbox checkall" style="float: left;margin: 0;">
	<label>
		<input type="checkbox" title="'.strip_tags($this->deleteButtonText).'">
	</label>
</div>'.strip_tags($this->deleteButtonText);
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
					$this->setIncludes($value->getIncludes());
					if ($value->getInputPosition() == 'main') $this->rollColumn += 1;
					if ($value->getFieldName()) {
						$select[$key] = $value->getFieldName();
						if ($key != $select[$key]) $select[$key] .= ' AS `'.$key.'`';
					}
				}
				if ($select) {
					if ($this->deleteTool and isset($_POST[$this->table.'_'.$this->init.'_delete'])) {
						foreach (@(array)$_POST[$this->table.'_'.$this->init.'_ids'] as $key => $value) {
							if (isset($_POST[$this->table.'_'.$this->init.'_delete_item'][$key])) {
								$this->db->delete($this->table, [$this->table_id => $value]);
							}
						}
						$this->msg = str_replace('{msg}', $this->successDeleteMsg, $this->successMsgTpl);
					}
					if ($this->saveTool and isset($_POST[$this->table.'_'.$this->init.'_submit'])) {
						$isValid = 1;
						$values  = array();
						foreach (@(array)$_POST[$this->table.'_'.$this->init.'_ids'] as $key => $value) {
							foreach ($select as $key1 => $value1) {
								if ($isValid) {
									if (!$this->input->$key1->getPlainText()) {
										$values[$value][$key1] = $this->input->$key1->getPostValue($key);
										$failMsg              = $this->input->$key1->getFailMsg();
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
					$select['roll_id']   = $this->table_id.' AS `roll_id`';
					$this->rollValues    = $this->db->getAll('SELECT SQL_CALC_FOUND_ROWS '.implode(' , ', $select).' FROM '.$this->table.' '.$this->getSort().' LIMIT '.@intval($_GET[$this->paginationConfig['get_name']])*intval($this->paginationConfig['per_page']).','.intval($this->paginationConfig['per_page']));
					$this->rollFoundRows = intval($this->db->getOne('SELECT FOUND_ROWS()'));
					foreach ($this->rollValues as $key => $value) {
						foreach ($this->input as $key1 => $value1) {
							if (isset($value[$key1])) {
								$value1->setValue($value[$key1], $key);
								$value1->setValueID($value['roll_id'], $key);
							}
						}
					}
					if ($this->deleteTool) {
						$this->setIncludes(['js' => ['checkall.min']]);
						foreach ($this->rollValues as $key => $value) {
							$value_delete = 1;
							foreach ($this->rollDeleteCondition as $value1) {
								foreach ($value as $key2 => $value2) {
									$value1 = str_replace('{'.$key2.'}', $value2, $value1);
								}
								eval('if ('.$value1.') $value_delete = 0;');
							}
							if ($value_delete) $this->rollDeleteInput[] = $key;
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
									foreach ($this->input as $key1 => $value1) {
										if ($value1->getInputPosition() == 'main') $this->form .= '<th>'.$value1->getRollTitle($this->sortConfig, @$_GET[$this->sortConfig['get_name']] , @$_GET[$this->sortConfig['get_name'].'_desc']).'</th>';
									}
								}
								if ($this->deleteTool) $this->form .= '<th>'.$this->getRollDeleteTitle().'</th>'; 
							$this->form .= $this->formTableItemHeaderBefore;
						$this->form .= $this->formTableHeaderAfter;
						$this->form .= $this->formTableBodyBefore;
							foreach ($this->rollValues as $key => $value) {
								$this->form .= $this->formTableItemBodyBefore;
								if (isset($this->input)) {
									$this->form .= '<input type="hidden" name="'.$this->table.'_'.$this->init.'_ids['.$key.']" value="'.$value['roll_id'].'">';
									foreach ($this->input as $value1) {
										if ($value1->getInputPosition() == 'main') $this->form .= $value1->getForm($key);
									}
									if ($this->deleteTool) $this->form .= '<td>'.$this->getRollDeleteInput($key).'</td>'; 
								}
								$this->form .= $this->formTableItemBodyAfter;
							}
						$this->form .= $this->formTableBodyAfter;
						$this->form .= $this->formTableFooterBefore;
							$this->form .= $this->formTableItemFooterBefore;
								$this->form .= '<td colspan="'.$this->rollColumn.'"><table style="width: 100%;"><tbody><tr>';
									$this->form .= '<td>';
										if ($this->returnUrl and $this->returnTool) $this->form .= '<a href="'.$this->returnUrl.'" class="'.$this->returnButtonClass.'">'.$this->returnButtonText.'</a>&nbsp;';
										if ($this->saveTool) $this->form .= '<button type="submit" name="'.$this->table.'_'.$this->init.'_submit" value="'.$this->init.'" class="'.$this->saveButtonClass.'">'.$this->saveButtonText.'</button>&nbsp;';
									$this->form .= '</td>';
									$this->form .= '<td style="text-align: center;">'.$this->getPagination().'</td>';
								$this->form .= '</tr></tbody></table></td>';
								if ($this->deleteTool) $this->form .= '<td><button type="submit" name="'.$this->table.'_'.$this->init.'_delete" value="'.$this->init.'" class="'.$this->deleteButtonClass.'" onclick="return confirm(\''.strip_tags($this->deleteButtonText).' ?\')">'.$this->deleteButtonText.'</button></td>';
							$this->form .= $this->formTableItemFooterBefore;
						$this->form .= $this->formTableFooterAfter;
					$this->form .= $this->formTableAfter;
				$this->form .= $this->formBodyAfter;
			$this->form .= $this->formAfter;
		$this->form .= '</form>';
		$this->form .= $this->getIncludes();
		return $this->form;
	}
}
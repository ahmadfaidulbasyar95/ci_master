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
	public $reportType                = array();
	public $reportTypeText            = array(
		'excel' => '<i class="fa fa-file-excel-o"></i>',
		'pdf'   => '<i class="fa fa-file-pdf-o"></i>',
		'table' => '<i class="fa fa-table"></i>',
		'json'  => '<i class="fa fa-file-code-o"></i>',
	);
	public $displayColumnTool         = 0;
	public $displayColumnButtonText   = 'Show/Hide Column <span class="caret"></span>';
	public $displayColumnButtonClass  = 'btn btn-default btn-xs';
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
		'num_links'       => 7,
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
			foreach ($this->input as $key => $value) {
				if ($value->getFieldName()) {
					if ($_GET[$this->sortConfig['get_name']] == $key) {
						$ret = preg_replace('~\s[O|o][R|r][D|d][E|e][R|r]\s[B|b][Y|y]\s.*?$~', '', $ret);
						$ret .= ' ORDER BY '.$value->getFieldName();
						if (@$_GET[$this->sortConfig['get_name'].'_desc']) $ret .= ' DESC'; 
					}
				}
			}
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

	public function addReport($reportType = array('excel', 'pdf'))
	{
		if (is_array($reportType)) {
			foreach ($reportType as $value) {
				$this->addReport($value);
			}
		}else{
			if (in_array($reportType, array_keys($this->reportTypeText)) and !in_array($reportType, $this->reportType)) {
				$this->reportType[] = $reportType;
			}
		}
	}

	public function addReportAll()
	{
		$this->addReport(array_keys($this->reportTypeText));
	}

	public function setDisplayColumnButton($text = '', $class = '')
	{
		if ($text) $this->displayColumnButtonText   = $text;
		if ($class) $this->displayColumnButtonClass = $class;
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
			$select = array();
			if (isset($_POST[$this->table.'_display_submit'])) {
				$_SESSION['pea_roll_display'][$this->table] = @(array)$_POST[$this->table.'_display'];
			}
			if (isset($_POST[$this->table.'_display_reset'])) {
				if (isset($_SESSION['pea_roll_display'][$this->table])) unset($_SESSION['pea_roll_display'][$this->table]);
			}
			if (isset($_SESSION['pea_roll_display'][$this->table])) {
				foreach ($this->input as $key => $value) {
					if ($value->displayColumnTool) {
						$value->setDisplayColumn(@intval($_SESSION['pea_roll_display'][$this->table][$key]));
					}
				}
			}
			foreach ($this->input as $key => $value) {
				$this->setIncludes($value->getIncludes());
				if ($value->displayColumnTool) {
					$this->displayColumnTool = 1;
					if (!$value->displayColumn) $value->setInputPosition('hidden');
				}
				if ($value->getInputPosition() == 'main') $this->rollColumn += 1;
				if ($value->getFieldName()) {
					$select[$key] = $value->getFieldName();
					if ($key != $select[$key]) $select[$key] .= ' AS `'.$key.'`';
				}
			}
			if ($select) {
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
				unset($select['roll_id']);
				if ($this->deleteTool and isset($_POST[$this->table.'_'.$this->init.'_delete'])) {
					foreach (@(array)$_POST[$this->table.'_'.$this->init.'_ids'] as $key => $value) {
						if (isset($_POST[$this->table.'_'.$this->init.'_delete_item'][$key])) {
							if ($this->onDeleteFunction) call_user_func($this->onDeleteFunction, $value, $this);
							$this->db->delete($this->table, [$this->table_id => $value]);
							foreach ($select as $key1 => $value1) {
								$this->input->$key1->onDeleteSuccess($key);
							}
						}
					}
					$this->msg = str_replace('{msg}', $this->successDeleteMsg, $this->successMsgTpl).$this->onDeleteReloadParentScript;
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
						$this->msg = str_replace('{msg}', $this->successMsg, $this->successMsgTpl).$this->onSaveReloadParentScript;
						foreach (@(array)$_POST[$this->table.'_'.$this->init.'_ids'] as $key => $value) {
							foreach ($select as $key1 => $value1) {
								$this->input->$key1->onSaveSuccess($key);
							}
						}
						foreach ($values as $key => $value) {
							if ($this->onSaveFunction) call_user_func($this->onSaveFunction, $key, $this);
						}
					}else{
						foreach (@(array)$_POST[$this->table.'_'.$this->init.'_ids'] as $key => $value) {
							foreach ($select as $key1 => $value1) {
								$this->input->$key1->onSaveFailed($key);
							}
						}
					}
				}
				if ($this->reportType and isset($_POST[$this->table.'_report'])) {
					if (in_array($_POST[$this->table.'_report'], $this->reportType)) {
						$reportData = $this->db->getAll('SELECT '.implode(' , ', $select).' FROM '.$this->table);
						if ($reportData) {
							$reportOutput = array();
							foreach ($reportData as $key => $value) {
								foreach ($this->input as $key1 => $value1) {
									if (isset($value[$key1]) and $value1->getInputPosition() == 'main') {
										$reportOutput[$key][$value1->title] = $value1->getReportOutput($value[$key1]);
									}
								}			
							}
							include_once dirname(__FILE__).'/../path.php';
							$reportPath = $this->_root.'application/cache/report/';
							$reportFile = time().'_'.mt_rand(10000000,999999999);
							lib_path_create($reportPath);
							file_put_contents($reportPath.$reportFile.'.json', json_encode($reportOutput));
							redirect($this->url.'_Pea/report/'.$_POST[$this->table.'_report'].'/'.$reportFile);
						}
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

	public function getForm()
	{
		$this->action();
		$this->form .= $this->formBefore;
			$this->form .= $this->formHeaderBefore;
				$this->form .= $this->formHeader;
			$this->form .= $this->formHeaderAfter;
			$this->form .= $this->formBodyBefore;
				$this->form .= '<form class="form_pea_roll" autocomplete="off" method="POST" action="" enctype="multipart/form-data">';
					$this->form .= $this->msg;
					$this->form .= $this->formTableBefore;
						$this->form .= $this->formTableHeaderBefore;
							$this->form .= $this->formTableItemHeaderBefore;
								foreach ($this->input as $key1 => $value1) {
									if ($value1->getInputPosition() == 'main') $this->form .= '<th>'.$value1->getRollTitle($this->sortConfig, @$_GET[$this->sortConfig['get_name']] , @$_GET[$this->sortConfig['get_name'].'_desc']).'</th>';
								}
								if ($this->deleteTool) $this->form .= '<th>'.$this->getRollDeleteTitle().'</th>'; 
							$this->form .= $this->formTableItemHeaderBefore;
						$this->form .= $this->formTableHeaderAfter;
						$this->form .= $this->formTableBodyBefore;
							foreach ($this->rollValues as $key => $value) {
								$this->form .= $this->formTableItemBodyBefore;
								$this->form .= '<input type="hidden" name="'.$this->table.'_'.$this->init.'_ids['.$key.']" value="'.$value['roll_id'].'">';
								foreach ($this->input as $value1) {
									if ($value1->getInputPosition() == 'main') $this->form .= $value1->getForm($key);
								}
								if ($this->deleteTool) $this->form .= '<td>'.$this->getRollDeleteInput($key).'</td>'; 
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
									if ($this->displayColumnTool) {
										$this->form .= '<td>';
											$this->form .= '<div class="dropup">';
												$this->form .= '<button class="'.$this->displayColumnButtonClass.' dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$this->displayColumnButtonText.'</button>';
												$this->form .= '<ul class="dropdown-menu">';
													foreach ($this->input as $key => $value) {
														if ($value->displayColumnTool) {
															$this->form .= '
															<li style="padding: 0 15px;">
																<div class="checkbox">
																	<label><input type="checkbox" name="'.$this->table.'_display['.$key.']" value="1" title="'.$value->title.'" onchange="$(this).parents(\'.dropup\').addClass(\'open\');"'.(($value->displayColumn) ? ' checked="checked"' : '').'>'.$value->title.'</label>
																</div>
															</li>';
														}
													}
													$this->form .= '
													<li style="padding: 0 15px;">
														<button type="submit" name="'.$this->table.'_display_submit" title="SUBMIT" value="'.$this->init.'" class="btn btn-default btn-sm" style="width: 50%;"><i class="fa fa-send"></i></button>
														<button type="submit" name="'.$this->table.'_display_reset" title="RESET" value="'.$this->init.'" class="btn btn-default btn-sm pull-right" style="width: calc(50% - 15px);"><i class="fa fa-times"></i></button>
													</li>';
												$this->form .= '</ul>';
											$this->form .= '</div>';
										$this->form .= '</td>';
									}
									if ($this->reportType) {
										$this->setIncludes(['js' => ['report.min']]);
										$this->form .= '<td><small>Export : </small>';
											$this->form .= '<div class="btn-group form_pea_roll_report">';
												foreach ($this->reportType as $value) {
													$this->form .= '<button type="submit" name="'.$this->table.'_report" title="Export '.strtoupper($value).'" value="'.$value.'" class="btn btn-default btn-sm">'.$this->reportTypeText[$value].'</button> ';
												}
											$this->form .= '</div>';
										$this->form .= '</td>';
									}
									$this->form .= '<td style="text-align: center;">'.$this->getPagination().'</td>';
								$this->form .= '</tr></tbody></table></td>';
								if ($this->deleteTool) $this->form .= '<td><button type="submit" name="'.$this->table.'_'.$this->init.'_delete" value="'.$this->init.'" class="'.$this->deleteButtonClass.'" onclick="return confirm(\''.strip_tags($this->deleteButtonText).' ?\')">'.$this->deleteButtonText.'</button></td>';
							$this->form .= $this->formTableItemFooterBefore;
						$this->form .= $this->formTableFooterAfter;
					$this->form .= $this->formTableAfter;
				$this->form .= '</form>';
			$this->form .= $this->formBodyAfter;
		$this->form .= $this->formAfter;
		$this->form .= $this->getIncludes();
		return $this->form;
	}
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/../pagination.php';
include_once __DIR__.'/pea_edit.php';
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
		'html'  => '<i class="fa fa-file-text-o"></i>',
		'json'  => '<i class="fa fa-file-code-o"></i>',
	);
	public $dataEmptyMsg    = '<td colspan="{columnSpan}" style="text-align:center;">No Data Available</td>';
	public $rollValues      = array();
	public $deleteInput     = array();
	public $deleteCondition = array();
	public $columnSpan      = 0;
	public $foundRows       = 0;
	public $sortConfig      = array(
		'get_name' => 'sort',
		'base_url' => '',
	);

	function __construct($opt)
	{
		parent::__construct($opt);

		$this->setSaveButton('<i class="fa fa-save"></i> Save'); 
		$this->setSuccessMsg('Success Save Data'); 
		$this->setDeleteTool(1); 
		
		$this->tableWrap('<div class="table-responsive" style="width: 100%;"><table class="pea_roll_table table table-striped table-bordered table-hover">','</table></div>');
		$this->tableHeaderWrap('<thead>','</thead>');
		$this->tableBodyWrap('<tbody>','</tbody>');
		$this->tableFooterWrap('<tfoot>','</tfoot>');
		$this->tableItemHeaderWrap('<tr>','</tr>');
		$this->tableItemBodyWrap('<tr>','</tr>');
		$this->tableItemFooterWrap('<tr>','</tr>');

		$this->setSortConfig('base_url', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		
		$this->displayColumnTool        = 0;
		$this->displayColumnButtonText  = 'Show/Hide Column <span class="caret"></span>';
		$this->displayColumnButtonClass = lib_bsv('btn btn-default btn-xs', 'btn btn-secondary btn-sm');

		$this->paginationConfig = array(
			'get_name'        => 'page',
			'base_url'        => $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'],
			'num_links'       => 7,
			'per_page'        => 20,
			'prev_msg'        => lib_bsv('<h5 class="pull-right text-muted">Result {from} - {to} from total {total}</h5>', '<h6 class="float-left text-muted" style="margin: 5px;">Result {from} - {to} from total {total}</h6>'),
			'full_tag_open'   => lib_bsv('<ul class="pagination pagination-sm" style="margin:0; padding-top:3px;">', '<nav class="float-left" aria-label="..."><ul class="pagination pagination-sm" style="margin:0; padding-top:3px;">'),
			'first_tag_open'  => lib_bsv('<li>', '<li class="page-item">'),
			'first_link'      => '&laquo;&laquo;',
			'first_tag_close' => '</li>',
			'prev_tag_open'   => lib_bsv('<li>', '<li class="page-item">'),
			'prev_link'       => '&laquo;',
			'prev_tag_close'  => '</li>',
			'num_tag_open'    => lib_bsv('<li>', '<li class="page-item">'),
			'num_tag_close'   => '</li>',
			'num_link'        => lib_bsv('<a href="{link}">{title}</a>', '<a class="page-link" href="{link}">{title}</a>'),
			'cur_tag_open'    => lib_bsv('<li class="active">', '<li class="page-item active" aria-current="page">'),
			'cur_tag_close'   => '</li>',
			'next_tag_open'   => lib_bsv('<li>', '<li class="page-item">'),
			'next_link'       => '&raquo;',
			'next_tag_close'  => '</li>',
			'last_tag_open'   => lib_bsv('<li>', '<li class="page-item">'),
			'last_link'       => '&raquo;&raquo;',
			'last_tag_close'  => '</li>',
			'full_tag_close'  => lib_bsv('</ul>', '</ul></nav>'),
			'go_tag_open'     => lib_bsv('<ul class="pagination pagination-sm" style="margin:0; padding-top:3px;"><li>', '<nav class="float-left" aria-label="..."><ul class="pagination pagination-sm" style="margin:5px;"><li>'),
			'go_question'     => 'Go to page ? of {totalpage}',
			'go_link'         => 'Go to',
			'go_tag_close'    => '</li></ul>',
		);
	}

	public function setPaginationConfig($name = '', $value = '')
	{
		if ($name and $value and isset($this->paginationConfig[$name])) $this->paginationConfig[$name] = $value; 
	}

	public function getPagination()
	{
		return lib_pagination($this->foundRows, intval($this->paginationConfig['per_page']), @intval($_GET[$this->paginationConfig['get_name']]), $this->paginationConfig['get_name'], $this->paginationConfig['base_url'], $this->paginationConfig['num_links'], 0 , $this->paginationConfig);
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

	public function setDataEmpty($msg='')
	{
		$this->dataEmptyMsg = $msg;
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

	public function getDeleteInput($index = 0)
	{
		if (in_array($index, $this->deleteInput)) {
			return '
<div class="'.lib_bsv('checkbox', 'form-check').'">
	<label>
		<input type="checkbox" name="'.$this->table.'_'.$this->init.'_'.$this->saveButtonName.'_delete_item['.$index.']" value="1" title="'.strip_tags($this->deleteButtonText).'">
		'.strip_tags($this->deleteButtonText).'
	</label>
</div>';
		}else return '';
	}

	public function getDeleteTitle()
	{
		return '
<div class="'.lib_bsv('checkbox', 'form-check').' checkall" style="float: left;margin: 0;">
	<label>
		<input type="checkbox" title="'.strip_tags($this->deleteButtonText).'">'.strip_tags($this->deleteButtonText).'
	</label>
</div>';
	}

	public function setDeleteCondition($condition = '') // if ({$condition}) -> use {} to get value of field
	{
		if ($condition) $this->deleteCondition[] = $condition;
	}

	public function action()
	{
		if (!$this->do_action) {
			$this->do_action = 1;
			foreach ($this->input as $key => $value) {
				if ($value->type == 'multiinput') {
					foreach ($value->element as $key1 => $value1) {
						$this->input->$key1 = $value1;
					}
				}
			}
			$select = array();
			if (isset($_POST[$this->table.'_'.$this->saveButtonName.'_display_submit'])) {
				$_SESSION['pea_roll_display'][$this->table] = @(array)$_POST[$this->table.'_display'];
			}
			if (isset($_POST[$this->table.'_'.$this->saveButtonName.'_display_reset'])) {
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
					if (!$value->displayColumn){
						$value->setInputPosition('hidden');
						$value->setPlainText();
					}
				}
				if ($value->getInputPosition() == 'main') $this->columnSpan += 1;
				if ($value->getFieldName()) {
					$select[$key] = $value->getFieldName();
					if ($key != $select[$key]) $select[$key] .= ' AS `'.$key.'`';
				}
			}
			if ($select) {
				$select['roll_id'] = $this->table_id.' AS `roll_id`';
				$this->rollValues  = $this->db->getAll('SELECT SQL_CALC_FOUND_ROWS '.implode(' , ', $select).' FROM '.$this->table.' '.$this->getSort().' LIMIT '.@intval($_GET[$this->paginationConfig['get_name']])*intval($this->paginationConfig['per_page']).','.intval($this->paginationConfig['per_page']));
				$this->foundRows   = intval($this->db->getOne('SELECT FOUND_ROWS()'));
				foreach ($this->rollValues as $key => $value) {
					foreach ($this->input as $key1 => $value1) {
						if (isset($value[$key1])) {
							$value1->setValue($value[$key1], $key);
							$value1->setValueID($value['roll_id'], $key);
						}
					}
				}
				unset($select['roll_id']);
				if ($this->deleteTool and isset($_POST[$this->table.'_'.$this->init.'_'.$this->saveButtonName.'_delete'])) {
					foreach (@(array)$_POST[$this->table.'_'.$this->init.'_ids'] as $key => $value) {
						if (isset($_POST[$this->table.'_'.$this->init.'_'.$this->saveButtonName.'_delete_item'][$key])) {
							if ($this->onDeleteFunction) call_user_func($this->onDeleteFunction, $value, $this);
							$this->db->delete($this->table, [$this->table_id => $value]);
							foreach ($select as $key1 => $value1) {
								$this->input->$key1->onDeleteSuccess($key);
							}
						}
					}
					$this->msg = str_replace('{msg}', $this->successDeleteMsg, $this->successMsgTpl).$this->onDeleteReloadParentScript;
				}
				if ($this->saveTool and isset($_POST[$this->table.'_'.$this->init.'_'.$this->saveButtonName])) {
					$isValid = 1;
					$values  = array();
					foreach (@(array)$_POST[$this->table.'_'.$this->init.'_ids'] as $key => $value) {
						foreach ($select as $key1 => $value1) {
							if ($isValid) {
								if (!$this->input->$key1->getPlainText()) {
									$values[$value][$key1] = $this->input->$key1->getPostValue($key);
									if (is_array($values[$value][$key1])) {
										foreach ($values[$value][$key1] as $key2 => $value2) {
											$values[$value][$key2] = $value2;
										}
									}
									$failMsg = $this->input->$key1->getFailMsg();
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
								$this->input->$key1->onSaveSuccess($key, $value);
							}
						}
						if ($this->onSaveFunction) {
							foreach ($values as $key => $value) {
								call_user_func($this->onSaveFunction, $key, $this);
							}
						}
					}else{
						foreach (@(array)$_POST[$this->table.'_'.$this->init.'_ids'] as $key => $value) {
							foreach ($select as $key1 => $value1) {
								$this->input->$key1->onSaveFailed($key);
							}
						}
					}
				}
				$select['roll_id']   = $this->table_id.' AS `roll_id`';
				if ($this->reportType and isset($_POST[$this->table.'_'.$this->saveButtonName.'_report'])) {
					if (in_array($_POST[$this->table.'_'.$this->saveButtonName.'_report'], $this->reportType)) {
						$reportData = $this->db->getAll('SELECT '.implode(' , ', $select).' FROM '.$this->table.' '.$this->getSort());
						if ($reportData) {
							$reportOutput = array();
							foreach ($reportData as $key => $value) {
								foreach ($this->input as $key1 => $value1) {
									if ((isset($value[$key1]) and $value1->getInputPosition() == 'main') or $value1->type == 'multiinput') {
										if ($value1->type == 'multiinput') {
											$value_report = $value1->getReportOutput($value, $_POST[$this->table.'_'.$this->saveButtonName.'_report'], $value['roll_id'], $key, $reportData);
										}else{
											$value_report = $value1->getReportOutput($value[$key1], $_POST[$this->table.'_'.$this->saveButtonName.'_report'], $key, $reportData);
										}
										if ($value1->displayReportFunction) {
											$value_report = call_user_func($value1->displayReportFunction, $value_report, $value['roll_id'], $key, $reportData);
										}
										$reportOutput[$key][$value1->title] = $value_report;
									}
								}			
							}
							include_once __DIR__.'/../file.php';
							$reportPath = $this->_root.'files/cache/report/';
							$reportFile = time().'_'.mt_rand(10000000,999999999);
							lib_file_write($reportPath.$reportFile.'.json', json_encode($reportOutput));
							redirect($this->url.'_T/report/'.$_POST[$this->table.'_'.$this->saveButtonName.'_report'].'/'.$reportFile);
						}
					}
				}
				$this->rollValues = $this->db->getAll('SELECT SQL_CALC_FOUND_ROWS '.implode(' , ', $select).' FROM '.$this->table.' '.$this->getSort().' LIMIT '.@intval($_GET[$this->paginationConfig['get_name']])*intval($this->paginationConfig['per_page']).','.intval($this->paginationConfig['per_page']));
				$this->foundRows  = intval($this->db->getOne('SELECT FOUND_ROWS()'));
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
						foreach ($this->deleteCondition as $value1) {
							foreach ($value as $key2 => $value2) {
								$value1 = str_replace('{'.$key2.'}', $value2, $value1);
							}
							eval('if ('.$value1.') $value_delete = 0;');
						}
						if ($value_delete) $this->deleteInput[] = $key;
					}
				}
			}
		}
	}

	public function getForm()
	{
		$btn_default = lib_bsv('btn btn-default', 'btn btn-secondary');
		$this->action();
		$this->form .= $this->formBefore;
			$this->form .= $this->formHeaderBefore;
				$this->form .= $this->formHeader;
			$this->form .= $this->formHeaderAfter;
			$this->form .= $this->formBodyBefore;
				$this->form .= '<form class="form_pea_roll" autocomplete="off" method="POST" action="" enctype="multipart/form-data" data-pagination_offset="'.(@intval($_GET[$this->paginationConfig['get_name']])*intval($this->paginationConfig['per_page'])).'">';
					$this->form .= $this->msg;
					$this->form .= $this->formTableBefore;
						$this->form .= $this->formTableHeaderBefore;
							$this->form .= $this->formTableItemHeaderBefore;
								foreach ($this->input as $key1 => $value1) {
									if ($value1->getInputPosition() == 'main') $this->form .= '<th>'.$value1->getRollTitle($this->sortConfig, @$_GET[$this->sortConfig['get_name']] , @$_GET[$this->sortConfig['get_name'].'_desc']).'</th>';
								}
								if ($this->deleteTool) $this->form .= '<th>'.$this->getDeleteTitle().'</th>'; 
							$this->form .= $this->formTableItemHeaderBefore;
						$this->form .= $this->formTableHeaderAfter;
						$this->form .= $this->formTableBodyBefore;
							if ($this->rollValues) {
								foreach ($this->rollValues as $key => $value) {
									$this->form .= $this->formTableItemBodyBefore;
									$this->form .= '<input type="hidden" name="'.$this->table.'_'.$this->init.'_ids['.$key.']" value="'.$value['roll_id'].'">';
									foreach ($this->input as $value1) {
										if ($value1->getInputPosition() == 'main') $this->form .= $value1->getForm($key, $this->rollValues);
									}
									if ($this->deleteTool) $this->form .= '<td>'.$this->getDeleteInput($key).'</td>'; 
									$this->form .= $this->formTableItemBodyAfter;
								}
							}else{
								$this->form .= $this->formTableItemBodyBefore;
								$this->form .= str_replace('{columnSpan}', ($this->deleteTool) ? $this->columnSpan+1 : $this->columnSpan, $this->dataEmptyMsg);
								$this->form .= $this->formTableItemBodyAfter;
							}
						$this->form .= $this->formTableBodyAfter;
						$this->form .= $this->formTableFooterBefore;
							$this->form .= $this->formTableItemFooterBefore;
								$this->form .= '<td colspan="'.$this->columnSpan.'" '.lib_bsv('', 'style="padding: 0;"').'><table style="width: 100%;"><tbody><tr style="background-color: inherit;">';
									$this->form .= '<td>';
										if ($this->returnUrl and $this->returnTool) $this->form .= '<a href="'.$this->returnUrl.'" class="'.$this->returnButtonClass.'">'.$this->returnButtonText.'</a>&nbsp;';
										if ($this->saveTool) $this->form .= '<button type="submit" name="'.$this->table.'_'.$this->init.'_'.$this->saveButtonName.'" value="'.$this->init.'" class="'.$this->saveButtonClass.'">'.$this->saveButtonText.'</button>&nbsp;';
									$this->form .= '</td>';
									if ($this->displayColumnTool) {
										$this->setIncludes(['js' => ['display_column.min']]);
										$this->form .= '<td style="padding-left: 15px;">';
											$this->form .= '<div class="pea_roll_display dropup">';
												$this->form .= '<button class="'.$this->displayColumnButtonClass.' dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$this->displayColumnButtonText.'</button>';
												$this->form .= '<div class="dropdown-menu">';
													foreach ($this->input as $key => $value) {
														if ($value->displayColumnTool) {
															$this->form .= '
															<div class="col-xs-6" style="padding: 2px 0 2px 10px;">
																<div class="'.lib_bsv('checkbox', 'form-check').'">
																	<label style="white-space: nowrap;text-overflow: ellipsis;overflow: hidden;width: 100%;"><input type="checkbox" name="'.$this->table.'_display['.$key.']" value="1" title="'.$value->title.'"'.(($value->displayColumn) ? ' checked="checked"' : '').'>'.$value->title.'</label>
																</div>
															</div>';
														}
													}
													$this->form .= '
													<div class="col-xs-12" style="padding: 5px 10px 5px 10px;">
														<button type="submit" name="'.$this->table.'_'.$this->saveButtonName.'_display_submit" title="SUBMIT" value="'.$this->init.'" class="'.$btn_default.' btn-sm" style="width: 50%;"><i class="fa fa-send"></i></button>
														<button type="submit" name="'.$this->table.'_'.$this->saveButtonName.'_display_reset" title="RESET" value="'.$this->init.'" class="'.$btn_default.' btn-sm pull-right" style="width: calc(50% - 10px);"><i class="fa fa-times"></i></button>
													</div>';
												$this->form .= '</div>';
											$this->form .= '</div>';
										$this->form .= '</td>';
									}
									if ($this->reportType) {
										$this->setIncludes(['js' => ['report.min']]);
										$this->form .= '<td style="padding-left: 15px; min-width: 200px;"><small>Export : </small>';
											$this->form .= '<div class="btn-group form_pea_roll_report">';
												foreach ($this->reportType as $value) {
													$this->form .= '<button type="submit" name="'.$this->table.'_'.$this->saveButtonName.'_report" title="Export '.strtoupper($value).'" value="'.$value.'" class="'.$btn_default.' btn-sm">'.$this->reportTypeText[$value].'</button> ';
												}
											$this->form .= '</div>';
										$this->form .= '</td>';
									}
									$this->form .= '<td style="text-align: center;">'.$this->getPagination().'</td>';
								$this->form .= '</tr></tbody></table></td>';
								if ($this->deleteTool) $this->form .= '<td><button type="submit" name="'.$this->table.'_'.$this->init.'_'.$this->saveButtonName.'_delete" value="'.$this->init.'" class="'.$this->deleteButtonClass.'" onclick="return confirm(\''.strip_tags($this->deleteButtonText).' ?\')">'.$this->deleteButtonText.'</button></td>';
							$this->form .= $this->formTableItemFooterAfter;
						$this->form .= $this->formTableFooterAfter;
					$this->form .= $this->formTableAfter;
				$this->form .= '</form>';
			$this->form .= $this->formBodyAfter;
		$this->form .= $this->formAfter;
		$this->form .= $this->getIncludes();
		return $this->form;
	}
}
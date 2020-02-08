<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once dirname(__FILE__).'/../output.php';
include_once dirname(__FILE__).'/pea_edit.php';
class lib_pea_import extends lib_pea_edit
{
	public $values = array();
	public $sql    = array();

	function __construct($opt)
	{
		parent::__construct($opt);

		$this->setSuccessMsgTpl('{msg}');
		$this->setFailMsgTpl('{msg}<br>');
	}

	public function action()
	{
		if (!$this->do_action) {
			$this->do_action = 1;
			$this->setIncludes(['js' => ['xlsx/xlsx.min','xlsx/xlsx.jszip','xlsx/xlsx.init']]);
			if (isset($_POST['act'])) {
				if (in_array($_POST['act'], [$this->table.'_'.$this->init.'_verify', $this->table.'_'.$this->init.'_submit'])) {
					$select = array();
					foreach ($this->input as $key => $value) {
						$this->setIncludes($value->getIncludes());
						if ($value->getFieldName()) {
							$select[$key] = $value->getFieldName();
							if ($key != $select[$key]) $select[$key] .= ' AS `'.$key.'`';
						}
					}
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
						$this->msg = str_replace('{msg}', $this->successMsg, $this->successMsgTpl).$this->onSaveReloadParentScript;
						if ($_POST['act'] == $this->table.'_'.$this->init.'_submit') {
							$this->editValues['edit_id'] = $this->db->insert($this->table, $values);
							foreach ($select as $key => $value) {
								$this->input->$key->onSaveSuccess();
							}
							if ($this->onSaveFunction and @$this->editValues['edit_id']) call_user_func($this->onSaveFunction, @$this->editValues['edit_id'], $this);
						}
						lib_output_json(array(
							'ok'     => 1,
							'msg'    => $this->msg,
							'result' => array(
								'status' => 1
							),
						));
					}else{
						foreach ($select as $key => $value) {
							$this->input->$key->onSaveFailed();
						}
						lib_output_json(array(
							'ok'     => 0,
							'msg'    => $this->msg,
							'result' => array(),
						));
					}
				}
			}
		}
	}

	public function getForm()
	{
		$this->action();
		$this->form .= $this->getIncludes();
		$this->form .= '<form class="form_pea_import" autocomplete="off" method="POST" action="'.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'" data-submit_name="'.$this->table.'_'.$this->init.'" enctype="multipart/form-data">';
			$this->form .= $this->formBefore;
				$this->form .= $this->formHeaderBefore;
					$this->form .= $this->formHeader;
				$this->form .= $this->formHeaderAfter;
				$this->form .= $this->formBodyBefore;
					$fields = array();
					foreach ($this->input as $key1 => $value1) {
						$fields[$value1->getName()] = strtoupper($value1->title);
					}
					$post_name = array_keys($fields);
					$fields    = array_values($fields);
					ob_start();
					include 'pea_import_form.php';
					$this->form .= ob_get_clean();
				$this->form .= $this->formBodyAfter;
			$this->form .= $this->formAfter;
		$this->form .= '</form>';
		return $this->form;
	}
}
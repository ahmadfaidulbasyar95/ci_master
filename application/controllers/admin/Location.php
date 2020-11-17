<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Location extends CI_Controller 
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('_pea_model');
		$this->load->model('_tpl_model');
		$this->load->library('session');

		$this->_tpl_model->setTemplate('admin');
	}

	function index()
	{
		$id = @intval($_GET['id']);
		include_once APPPATH.'libraries/tabs.php';
		
		// $_GET['id']     = 0;
		// $_GET['par_id'] = $id;
		// ob_start();
		// $this->form();
		// $add = ob_get_clean();
		
		$_GET['id'] = $id;
		ob_start();
		$this->list();
		$list = ob_get_clean();

		if ($id) {
			// ob_start();
			// $this->form();
			// $edit = ob_get_clean();
			
			echo lib_tabs(array(
				// 'Edit Location'       => $edit,
				'Sub Location'        => $list,
				// 'Add Sub Location' => $add,
			));
		}else{
			echo lib_tabs(array(
				'Location'        => $list,
				// 'Add Location' => $add,
			));
		}

		$this->_tpl_model->show();
	}

	function list()
	{
		$id   = @intval($_GET['id']);
		$form = $this->_pea_model->newForm('location');

		$form->initRoll('WHERE `par_id`='.$id.' ORDER BY `title` ASC');

		$form->roll->addInput('title', 'sqllinks');
		$form->roll->input->title->setTitle('Title');
		$form->roll->input->title->setLinks('admin/location');
		
		$form->roll->setDeleteTool(false);
		$form->roll->setSaveTool(false);
		$form->roll->addReportAll();
		$form->roll->action();
		echo $form->roll->getForm();
	}

	function form()
	{
		$id     = @intval($_GET['id']);
		$par_id = @intval($_GET['par_id']);
		$form   = $this->_pea_model->newForm('location');

		$form->initEdit(!empty($id) ? 'WHERE `id`='.$id : '');
		
		$form->edit->setHeader(!empty($id) ? 'Edit Location' : 'Add Location');
		
		$form->edit->addInput('title','text');
		$form->edit->input->title->setTitle('Title');
		$form->edit->input->title->setRequire();

		if (!$id and $par_id) {
			$form->edit->addExtraField('par_id', $par_id);
		}
		
		$form->edit->action();
		echo $form->edit->getForm();
	}
}

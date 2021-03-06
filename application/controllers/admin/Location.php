<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Location extends CI_Controller 
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('_pea_model');
		$this->load->model('_tpl_model');

		$this->_tpl_model->user_login_validate(1);

		$this->_tpl_model->setTemplate('admin');
		$this->_tpl_model->nav_add('admin/dashboard/main', '<i class="fa fa-home"></i> Home', '0');
	}

	function index()
	{
		$id = @intval($_GET['id']);
		$this->_tpl_model->lib('tabs');
		
		$_GET['id']     = 0;
		$_GET['par_id'] = $id;
		ob_start();
		$this->form();
		$add = ob_get_clean();
		
		$_GET['id'] = $id;
		ob_start();
		$this->list();
		$list = ob_get_clean();

		if ($id) {
			ob_start();
			$this->form();
			$edit = ob_get_clean();
			
			echo lib_tabs(array(
				'Edit Location'    => $edit,
				'Sub Location'     => $list,
				'Add Sub Location' => $add,
			));
		}else{
			echo lib_tabs(array(
				'Location'     => $list,
				'Add Location' => $add,
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

		$form->roll->addInput('source_id', 'sqlplaintext');
		$form->roll->input->source_id->setTitle('Source ID');
		
		$form->roll->setRollDeleteCondition('"{source_id}" != ""');

		$form->roll->onDelete(function($id, $f)
		{
			$f->db->exec('DELETE FROM `location` WHERE `par_ids` LIKE "%\"'.$id.'\"%"');
		});

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

		if (!$id) {
			if ($par_id) {
				$parent = $form->db->getRow('SELECT `par_ids`,`type_id` FROM `location` WHERE `id`='.$par_id);
				if ($parent) {
					$parent['par_ids']   = (array)json_decode($parent['par_ids'], 1);
					$parent['par_ids'][] = (string)$par_id;

					$ret      = '&return='.urlencode($this->_tpl_model->_url_current);
					$par_data = $form->db->getAll('SELECT `id`,`title` FROM `location` WHERE `id` IN ('.implode(',', $parent['par_ids']).') ORDER BY `type_id`');
					foreach ($par_data as $value) {
						$this->_tpl_model->nav_add('admin/location?id='.$value['id'].$ret, $value['title']);
					}

					$form->edit->addExtraField('par_id', $par_id);
					$form->edit->addExtraField('par_ids', json_encode($parent['par_ids']));
					$form->edit->addExtraField('type_id', intval($parent['type_id']) + 1);
				}else{
					$form->edit->addExtraField('type_id', 1);
				}
			}else{
				$form->edit->addExtraField('type_id', 1);
			}
		}
		
		$form->edit->action();
		echo $form->edit->getForm();
	}
}

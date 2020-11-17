<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller 
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
		$form = $this->_pea_model->newForm('user');

		$form->initRoll('WHERE 1 ORDER BY `name` ASC');

		$form->roll->setHeader('User');

		$form->roll->addInput('name', 'sqllinks');
		$form->roll->input->name->setTitle('Name');
		$form->roll->input->name->setLinks('admin/user/form');

		$form->roll->addInput('image', 'file');
		$form->roll->input->image->setTitle('Image');
		$form->roll->input->image->setFolder('files/user/');
		$form->roll->input->image->setAllowedExtension(array('jpg', 'jpeg', 'gif', 'png', 'bmp'));
		$form->roll->input->image->setResize(1080);
		$form->roll->input->image->setThumbnail(120, 'thumb/');
		$form->roll->input->image->setPlainText(true);
		
		$form->roll->addReportAll();
		$form->roll->action();
		echo $form->roll->getForm();
		$this->_tpl_model->show();
	}

	function form()
	{
		$id   = @intval($_GET['id']);
		$form = $this->_pea_model->newForm('user');

		$form->initEdit(!empty($id) ? 'WHERE `id`='.$id : '');
		
		$form->edit->setHeader(!empty($id) ? 'Edit User' : 'Add User');
		
		$form->edit->addInput('name','text');
		$form->edit->input->name->setTitle('Name');

		$form->edit->addInput('image', 'file');
		$form->edit->input->image->setTitle('Image');
		$form->edit->input->image->setFolder('files/user/');
		$form->edit->input->image->setAllowedExtension(array('jpg', 'jpeg', 'gif', 'png', 'bmp'));
		$form->edit->input->image->setResize(1080);
		$form->edit->input->image->setThumbnail(120, 'thumb/');
		
		$form->edit->action();
		echo $form->edit->getForm();
		$this->_tpl_model->show();
	}
}

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
		$par_id = @intval($_GET['par_id']);
		$form   = $this->_pea_model->newForm('location');

		$form->initRoll('WHERE `par_id`='.$par_id.' ORDER BY `title` ASC');

		$form->roll->setHeader('Data Lokasi');

		$form->roll->addInput('title', 'sqllinks');
		$form->roll->input->title->setTitle('Nama');
		$form->roll->input->title->setLinks('admin/location/form');
		
		$form->roll->action();
		echo $form->roll->getForm();
		$this->_tpl_model->show();
	}

	function form()
	{
		$id   = @intval($_GET['id']);
		$form = $this->_pea_model->newForm('location');

		$form->initEdit(!empty($id) ? 'WHERE `id`='.$id : '');
		
		$form->edit->setHeader(!empty($id) ? 'Ubah Lokasi' : 'Tambah Lokasi');
		
		$form->edit->addInput('title','text');
		$form->edit->input->title->setTitle('Nama');
		
		$form->edit->action();
		echo $form->edit->getForm();
		$this->_tpl_model->show();
	}
}

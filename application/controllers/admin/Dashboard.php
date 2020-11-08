<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller 
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
		$this->_tpl_model->setLayout('blank');
		$this->_tpl_model->view('Dashboard/index');
		$this->_tpl_model->show();
	}

	function config()
	{
		$form = $this->_pea_model->newForm('config');

		$form->initEdit('WHERE `name`="dashboard"', 'name', 1);
		
		$form->edit->setHeader('Pengaturan Dashboard');

		$form->edit->addInput('value', 'params');
		$form->edit->input->value->setTitle('');
		$form->edit->input->value->addInput('desktop_background', 'file');
		$form->edit->input->value->element->desktop_background->setTitle('Gambar Desktop');
		$form->edit->input->value->element->desktop_background->setImageClick();

		$form->edit->action();
		echo $form->edit->getForm();
		$this->_tpl_model->show();
	}
}

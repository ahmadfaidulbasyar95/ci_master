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
		include_once $this->_tpl_model->_root.'application/libraries/tabs.php';
		$form = $this->_pea_model->newForm('config');

		$form->initEdit('WHERE `name`="site"', 'name', 1);		
		$form->edit->setHeader('Site');

		$form->edit->addInput('value', 'params');
		$form->edit->input->value->setTitle('');

		$form->edit->input->value->addInput('meta_title', 'text');
		$form->edit->input->value->element->meta_title->setTitle('Meta Title');
		$form->edit->input->value->element->meta_title->setRequire();

		$form->edit->input->value->addInput('meta_description', 'textarea');
		$form->edit->input->value->element->meta_description->setTitle('Meta Description');
		$form->edit->input->value->element->meta_description->setRequire();

		$form->edit->input->value->addInput('meta_keyword', 'text');
		$form->edit->input->value->element->meta_keyword->setTitle('Meta keyword');
		$form->edit->input->value->element->meta_keyword->setRequire();

		$form->edit->input->value->addInput('domain', 'text');
		$form->edit->input->value->element->domain->setTitle('Domain');
		$form->edit->input->value->element->domain->setRequire();

		$form->edit->input->value->addInput('icon', 'file');
		$form->edit->input->value->element->icon->setTitle('Icon');
		$form->edit->input->value->element->icon->setImageClick();
		$form->edit->input->value->element->icon->setRequire();

		$form->edit->input->value->addInput('logo', 'file');
		$form->edit->input->value->element->logo->setTitle('Logo');
		$form->edit->input->value->element->logo->setImageClick();
		$form->edit->input->value->element->logo->setRequire();

		$form->edit->input->value->addInput('footer', 'textarea');
		$form->edit->input->value->element->footer->setTitle('Footer');
		$form->edit->input->value->element->footer->setHtmlEditor();
		$form->edit->input->value->element->footer->setRequire();

		$form->edit->setSaveButton('','','site');
		$form->edit->action();
		$c_site = $form->edit->getForm();

		$form->initEdit('WHERE `name`="dashboard"', 'name', 1);		
		$form->edit->setHeader('Dashboard');

		$form->edit->addInput('value', 'params');
		$form->edit->input->value->setTitle('');

		$form->edit->input->value->addInput('desktop_background', 'file');
		$form->edit->input->value->element->desktop_background->setTitle('Desktop Background');
		$form->edit->input->value->element->desktop_background->setImageClick();
		$form->edit->input->value->element->desktop_background->setRequire();

		$form->edit->setSaveButton('','','dashboard');
		$form->edit->action();
		$c_dashboard = $form->edit->getForm();

		echo lib_tabs(array(
			'Site'      => $c_site,
			'Dashboard' => $c_dashboard,
		));

		$this->_tpl_model->show();
	}
}

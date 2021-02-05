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
		$this->_tpl_model->nav_add('admin/dashboard/main', '<i class="fa fa-home"></i> Home', '0');
	}

	function index()
	{
		$this->_tpl_model->setLayout('blank');
		$this->_tpl_model->view('Dashboard/index');
		$this->_tpl_model->show();
	}

	function main()
	{
		$this->_tpl_model->setLayout('blank');
		$this->_tpl_model->view('Dashboard/main');
		$this->_tpl_model->show();
	}

	function login()
	{
		$this->load->model('_encrypt_model');
		$input = array(
			'usr' => mt_rand(100000000000,500000000000),
			'pwd' => mt_rand(500000000001,900000000000),
			'msg' => '',
		);
		if (!empty($_POST['token'])) {
			$token = $this->_encrypt_model->decodeToken($_POST['token']);
			if ($token) {
				pr($token);
			}else{
				$input['msg'] = $this->_tpl_model->msg('Token Expired', 'danger');
			}
		}
		$input['token'] = $this->_encrypt_model->encodeToken($input['usr'].'|'.$input['pwd'], 2);

		$this->_tpl_model->setLayout('blank');
		$this->_tpl_model->view('Dashboard/login', ['input' => $input]);
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

		$form->edit->input->value->addInput('login_background', 'file');
		$form->edit->input->value->element->login_background->setTitle('Login Background');
		$form->edit->input->value->element->login_background->setImageClick();
		$form->edit->input->value->element->login_background->setRequire();

		$form->edit->input->value->addInput('login_uri', 'text');
		$form->edit->input->value->element->login_uri->setTitle('Login URI');
		$form->edit->input->value->element->login_uri->setRequire();
		$name = $form->edit->input->value->element->login_uri->getName();
		if (!empty($_POST[$name])) {
			$_POST[$name] = preg_replace('~[^a-z0-9]~', '', strtolower($_POST[$name]));
		}

		$form->edit->setSaveButton('','','dashboard');
		$form->edit->action();
		$c_dashboard = $form->edit->getForm();

		echo lib_tabs(array(
			'Site'      => $c_site,
			'Dashboard' => $c_dashboard,
		));

		if ($_POST) {
			$this->_tpl_model->clean_cache();
			$this->_tpl_model->config('dashboard');
		}

		$this->_tpl_model->show();
	}
}

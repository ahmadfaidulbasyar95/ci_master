<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notif extends CI_Controller 
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

	function telegram_config()
	{
		$form = $this->_pea_model->newForm('config');

		$form->initEdit('WHERE `name`="notif_telegram"', 'name', 1);		
		$form->edit->setHeader('Telegram Configuration');

		$form->edit->addInput('value', 'params');
		$form->edit->input->value->setTitle('');

		$form->edit->input->value->addInput('active', 'checkbox');
		$form->edit->input->value->element->active->setTitle('Enable');
		$form->edit->input->value->element->active->setCaption('Yes');
		$form->edit->input->value->element->active->addAttr('id="active"');

		$form->edit->input->value->addInput('token', 'text');
		$form->edit->input->value->element->token->setTitle('Bot Token');
		$form->edit->input->value->element->token->addAttr('id="token"');

		$form->edit->input->value->addInput('data', 'hidden');
		$form->edit->input->value->element->data->addAttr('id="data"');

		$form->edit->action();
		echo $form->edit->getForm().'<div id="info"></div>';

		if ($_POST) {
			$this->_tpl_model->clean_cache();
		}

		$this->_tpl_model->js('controllers/admin/notif_telegram_config.js');
		$this->_tpl_model->show();
	}

}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Credentials extends CI_Controller 
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

	function telegram()
	{
		$form = $this->_pea_model->newForm('config');

		$form->initEdit('WHERE `name`="telegram"', 'name', 1);		
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

		$this->_tpl_model->js('controllers/admin/credentials_telegram.js');
		$this->_tpl_model->show();
	}

	function google()
	{
		$form = $this->_pea_model->newForm('config');

		$form->initEdit('WHERE `name`="google"', 'name', 1);		
		$form->edit->setHeader('Google Configuration');

		$form->edit->addInput('value', 'params');
		$form->edit->input->value->setTitle('');

		$form->edit->input->value->addInput('client_id', 'text');
		$form->edit->input->value->element->client_id->setTitle('Client ID');
		$form->edit->input->value->element->client_id->setRequire();

		$form->edit->action();
		echo $form->edit->getForm();

		if ($_POST) {
			$this->_tpl_model->clean_cache();
		}

		$this->_tpl_model->show();
	}

	function email()
	{
		$form = $this->_pea_model->newForm('config');

		$form->initEdit('WHERE `name`="email"', 'name', 1);		
		$form->edit->setHeader('E-Mail Configuration');

		$form->edit->addInput('value', 'params');
		$form->edit->input->value->setTitle('');

		$form->edit->input->value->addInput('from_name', 'text');
		$form->edit->input->value->element->from_name->setTitle('Default From (Name)');
		$form->edit->input->value->element->from_name->setRequire();

		$form->edit->input->value->addInput('from_email', 'text');
		$form->edit->input->value->element->from_email->setTitle('Default From (E-Mail)');
		$form->edit->input->value->element->from_email->setType('email');
		$form->edit->input->value->element->from_email->setRequire();

		$form->edit->input->value->addInput('protocol', 'select');
		$form->edit->input->value->element->protocol->setTitle('Protocol');
		$form->edit->input->value->element->protocol->addOption('Mail','mail');
		$form->edit->input->value->element->protocol->addOption('Sendmail','sendmail');
		$form->edit->input->value->element->protocol->addOption('SMTP','smtp');

		$form->edit->input->value->addInput('smtp_host', 'text');
		$form->edit->input->value->element->smtp_host->setTitle('SMTP Server Address');

		$form->edit->input->value->addInput('smtp_user', 'text');
		$form->edit->input->value->element->smtp_user->setTitle('SMTP Username');

		$form->edit->input->value->addInput('smtp_pass', 'text');
		$form->edit->input->value->element->smtp_pass->setTitle('SMTP Password');
		$form->edit->input->value->element->smtp_pass->setType('password');

		$form->edit->input->value->addInput('smtp_port', 'text');
		$form->edit->input->value->element->smtp_port->setTitle('SMTP Port');

		$form->edit->input->value->addInput('smtp_timeout', 'text');
		$form->edit->input->value->element->smtp_timeout->setTitle('SMTP Timeout (in seconds)');

		$form->edit->input->value->addInput('smtp_crypto', 'select');
		$form->edit->input->value->element->smtp_crypto->setTitle('SMTP Encryption');
		$form->edit->input->value->element->smtp_crypto->addOption('None','');
		$form->edit->input->value->element->smtp_crypto->addOption('TLS','tls');
		$form->edit->input->value->element->smtp_crypto->addOption('SSL','ssl');

		$form->edit->action();
		echo $form->edit->getForm();

		if ($_POST) {
			$this->_tpl_model->clean_cache();
		}

		$this->_tpl_model->show();
	}

	function email_template()
	{
		echo $this->_tpl_model->button('admin/credentials/email_template_form', 'Add E-Mail Template', 'fa fa-plus', 'modal_reload modal_large', 'style="margin-bottom: 15px;"', 1);

		$form = $this->_pea_model->newForm('config_email');
	
		$form->initRoll('WHERE 1 ORDER BY `id` DESC');

		$form->roll->addInput('name', 'sqllinks');
		$form->roll->input->name->setTitle('ID');
		$form->roll->input->name->setLinks('admin/credentials/email_template_form');
		$form->roll->input->name->setModal();
		$form->roll->input->name->setModalReload();
		$form->roll->input->name->setModalLarge();

		$form->roll->addInput('from', 'sqlplaintext');
		$form->roll->input->from->setTitle('From');
		$form->roll->input->from->setFieldName('CONCAT(`from_name`,"|",`from_email`)');
		$form->roll->input->from->setDisplayFunction(function($value='')
		{
			$value = explode('|', $value);

			if (empty($value[0])) $value[0] = 'Default';
			if (empty($value[1])) $value[1] = 'Default';
			
			return $value[0].' ('.$value[1].')';
		});

		$form->roll->addInput('mailtype', 'select');
		$form->roll->input->mailtype->setTitle('Type');
		$form->roll->input->mailtype->addOption('Plain Text',1);
		$form->roll->input->mailtype->addOption('HTML',2);
		$form->roll->input->mailtype->setPlainText();

		$form->roll->addInput('subject', 'sqlplaintext');
		$form->roll->input->subject->setTitle('Subject');

		$form->roll->addInput('created', 'datetime');
		$form->roll->input->created->setTitle('Created');
		$form->roll->input->created->setPlainText();
		$form->roll->input->created->setDisplayColumn();

		$form->roll->addInput('updated', 'datetime');
		$form->roll->input->updated->setTitle('Updated');
		$form->roll->input->updated->setPlainText();
		$form->roll->input->updated->setDisplayColumn(false);

		$form->roll->setSaveTool(false);
		$form->roll->action();
		echo $form->roll->getForm();

		if ($_POST) {
			$tpls = $this->_db_model->getAssoc('SELECT `name`,`from_name`,`from_email`,`mailtype`,`subject`,`message` FROM `config_email`');
			lib_file_write($this->_pea_model->_root.'files/uploads/email_template', json_encode($tpls));
		}

		$this->_tpl_model->show();
	}
	function email_template_form()
	{
		$id   = @intval($_GET['id']);
		$form = $this->_pea_model->newForm('config_email');

		$_GET['return'] = '';
		$this->_tpl_model->setLayout('blank');
		
		$form->initEdit(!empty($id) ? 'WHERE `id`='.$id : '');

		$form->edit->setHeader(!empty($id) ? 'Edit E-Mail Template' : 'Add E-Mail Template');
		$form->edit->setModalResponsive();

		$form->edit->addInput('name', 'text');
		$form->edit->input->name->setTitle('ID');
		$form->edit->input->name->setUniq();
		$form->edit->input->name->setRequire();
		$name = $form->edit->input->name->getName();
		if (!empty($_POST[$name])) {
			$_POST[$name] = preg_replace('~[^a-z0-9]~', '_', strtolower($_POST[$name]));
		}

		$form->edit->addInput('from_name', 'text');
		$form->edit->input->from_name->setTitle('From (Name)');
		$form->edit->input->from_name->addTip('Keep empty to use default setting');

		$form->edit->addInput('from_email', 'text');
		$form->edit->input->from_email->setTitle('From (E-Mail)');
		$form->edit->input->from_email->setType('email');
		$form->edit->input->from_email->addTip('Keep empty to use default setting');

		$form->edit->addInput('mailtype', 'select');
		$form->edit->input->mailtype->setTitle('Type');
		$form->edit->input->mailtype->addOption('Plain Text',1);
		$form->edit->input->mailtype->addOption('HTML',2);
		$form->edit->input->mailtype->addAttr('id="s_mailtype"');

		$form->edit->addInput('subject', 'text');
		$form->edit->input->subject->setTitle('Subject');
		$form->edit->input->subject->setRequire();

		$form->edit->addInput('message', 'textarea');
		$form->edit->input->message->setTitle('Message');
		$form->edit->input->message->setRequire();
		$form->edit->input->message->setHtmlEditor();
		$form->edit->input->message->addAttr('rel="s_message" rows="20"');

		$mailtype = $form->edit->input->mailtype->getName();
		$message  = $form->edit->input->message->getName();
		if (!empty($_POST[$mailtype])) {
			if ($_POST[$mailtype] == 1) {
				$_POST[$message] = $_POST['__'.$message];
			}
		}

		$form->edit->action();
		$this->_tpl_model->js('controllers/admin/credentials_email_template_form.js');
		echo $form->edit->getForm();

		if ($_POST) {
			$tpls = $this->_db_model->getAssoc('SELECT `name`,`from_name`,`from_email`,`mailtype`,`subject`,`message` FROM `config_email`');
			lib_file_write($this->_pea_model->_root.'files/uploads/email_template', json_encode($tpls));
		}

		$this->_tpl_model->show();
	}

}

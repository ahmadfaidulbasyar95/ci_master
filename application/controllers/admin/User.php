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
		$this->_tpl_model->nav_add('admin/dashboard/main', '<i class="fa fa-home"></i> Home', '0');
	}

	function index()
	{
		echo $this->_tpl_model->button('admin/user/form?return='.urlencode($this->_tpl_model->_url_current), 'Add User', 'fa fa-plus');

		$form = $this->_pea_model->newForm('user');

		$form->initRoll('WHERE 1 ORDER BY `name` ASC');

		$form->roll->addInput('name', 'sqllinks');
		$form->roll->input->name->setTitle('Name');
		$form->roll->input->name->setLinks('admin/user/form');
		$form->roll->input->name->setModal();
		$form->roll->input->name->setModalReload();
		$form->roll->input->name->setModalLarge();

		$form->roll->addInput('username', 'sqlplaintext');
		$form->roll->input->username->setTitle('Username');
		$form->roll->input->username->setDisplayColumn();

		$form->roll->addInput('email', 'sqlplaintext');
		$form->roll->input->email->setTitle('Email');
		$form->roll->input->email->setDisplayColumn();

		$form->roll->addInput('phone', 'sqlplaintext');
		$form->roll->input->phone->setTitle('Phone');
		$form->roll->input->phone->setDisplayColumn();

		$form->roll->addInput('image', 'file');
		$form->roll->input->image->setTitle('Image');
		$form->roll->input->image->setFolder('files/user/');
		$form->roll->input->image->setAllowedExtension(array('jpg', 'jpeg', 'gif', 'png', 'bmp'));
		$form->roll->input->image->setResize(1080);
		$form->roll->input->image->setImageClick();
		$form->roll->input->image->setThumbnail(120, 'thumb/');
		$form->roll->input->image->setPlainText(true);
		$form->roll->input->image->setDisplayColumn();

		$form->roll->addInput('gender', 'select');
		$form->roll->input->gender->setTitle('Gender');
		$form->roll->input->gender->addOption('Male', 1);
		$form->roll->input->gender->addOption('Female', 2);
		$form->roll->input->gender->setPlainText();
		$form->roll->input->gender->setDisplayColumn();

		$form->roll->addInput('birth_date', 'date');
		$form->roll->input->birth_date->setTitle('Birthdate');
		$form->roll->input->birth_date->setDateFormat('d M Y');
		$form->roll->input->birth_date->setPlainText();
		$form->roll->input->birth_date->setDisplayColumn();

		$form->roll->addInput('active', 'checkbox');
		$form->roll->input->active->setTitle('Active');
		$form->roll->input->active->setCaption('yes');
		$form->roll->input->active->setDisplayColumn();
		
		$form->roll->addReportAll();
		$form->roll->action();
		echo $form->roll->getForm();
		$this->_tpl_model->show();
	}

	function form()
	{
		$id   = @intval($_GET['id']);
		$form = $this->_pea_model->newForm('user');

		if ($id) {
			$_GET['return'] = '';
			$this->_tpl_model->setLayout('blank');
		}

		$form->initEdit(!empty($id) ? 'WHERE `id`='.$id : '');
		
		$form->edit->setHeader(!empty($id) ? 'Edit User' : 'Add User');
		
		$form->edit->addInput('name','text');
		$form->edit->input->name->setTitle('Name');
		$form->edit->input->name->setRequire();
		
		$form->edit->addInput('username','text');
		$form->edit->input->username->setTitle('Username');
		$form->edit->input->username->setRequire();
		$form->edit->input->username->setUniq();

		if (!$id) {
			$form->edit->addInput('password','text');
			$form->edit->input->password->setTitle('Password');
			$form->edit->input->password->setRequire();
			$name = $form->edit->input->password->getName();
			if (!empty($_POST[$name])) {
				$this->load->model('_encrypt_model');
				$_POST[$name] = $this->_encrypt_model->encode($_POST[$name]);
			}
		}else{
			$form->edit->addInput('password', 'plaintext');
			$form->edit->input->password->setTitle('Password');
			$form->edit->input->password->setValue($this->_tpl_model->button('admin/user/pwd?id='.$id.'&return='.urlencode($this->_tpl_model->_url_current), 'Change', 'fa fa-repeat'));
		}
		
		$form->edit->addInput('email','text');
		$form->edit->input->email->setTitle('Email');
		$form->edit->input->email->setType('email');
		$form->edit->input->email->setUniq();
		
		$form->edit->addInput('phone','text');
		$form->edit->input->phone->setTitle('Phone');
		$form->edit->input->phone->setType('number');
		$form->edit->input->phone->setUniq();

		$form->edit->addInput('image', 'file');
		$form->edit->input->image->setTitle('Image');
		$form->edit->input->image->setFolder('files/user/');
		$form->edit->input->image->setAllowedExtension(array('jpg', 'jpeg', 'gif', 'png', 'bmp'));
		$form->edit->input->image->setResize(1080);
		$form->edit->input->image->setImageClick();
		$form->edit->input->image->setThumbnail(120, 'thumb/');

		$form->edit->addInput('gender', 'select');
		$form->edit->input->gender->setTitle('Gender');
		$form->edit->input->gender->addOption('-- Select Gender --', '');
		$form->edit->input->gender->addOption('Male', 1);
		$form->edit->input->gender->addOption('Female', 2);

		$form->edit->addInput('birth_date', 'date');
		$form->edit->input->birth_date->setTitle('Birthdate');

		$form->edit->addInput('active', 'checkbox');
		$form->edit->input->active->setTitle('Active');
		$form->edit->input->active->setCaption('yes');
		
		$form->edit->onSave(function($id, $f)
		{
			$data = $f->db->getRow('SELECT * FROM `user` WHERE `id`='.$id);
			if ($data) {
				$data_update = array();
				if ($data['location_id']) {
					$location = $f->db->getRow('SELECT `title`,`detail` FROM `location` WHERE `id`='.$data['location_id']);
					if ($location) {
						$data_update['location_title']  = $location['title'];
						$data_update['location_detail'] = $location['detail'];
					}
				}
				if ($data_update) {
					$f->db->update('user', $data_update, $id);
				}
			}
		});
		$form->edit->action();
		echo $form->edit->getForm();
		$this->_tpl_model->show();
	}

	function pwd()
	{
		$id = @intval($_GET['id']);
		$this->_tpl_model->setLayout('blank');
		if ($id) {
			$form = $this->_pea_model->newForm('user');
			
			$form->initEdit('WHERE `id`='.$id);
			
			$form->edit->setHeader('Change Password');

			$form->edit->addInput('name', 'sqlplaintext');
			$form->edit->input->name->setTitle('Name');

			$form->edit->addInput('username', 'sqlplaintext');
			$form->edit->input->username->setTitle('Username');
			
			$form->edit->addInput('password','text');
			$form->edit->input->password->setTitle('New Password');
			$form->edit->input->password->setRequire();
			$name = $form->edit->input->password->getName();
			if (!empty($_POST[$name])) {
				$this->load->model('_encrypt_model');
				$_POST[$name] = $this->_encrypt_model->encode($_POST[$name]);
			}

			$form->edit->action();
			$form->edit->input->password->setValue('');
			echo $form->edit->getForm();
			$this->_tpl_model->show();
		}
	}
}

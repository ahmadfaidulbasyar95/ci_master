<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller 
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('_pea_model');
		$this->load->model('_tpl_model');

		if ($this->router->method != 'login') {
			$this->_tpl_model->user_login_validate(1);
		}

		$this->_tpl_model->setTemplate('admin');
		$this->_tpl_model->nav_add('admin/dashboard/main', '<i class="fa fa-home"></i> Home', '0');

		$this->user_group_type = array(
			'Public' => 0,
			'Admin'  => 1,
		);
	}

	function index()
	{
		$form = $this->_pea_model->newForm('user');

		$form->initSearch();

		$form->search->addInput('keyword', 'keyword');
		$form->search->input->keyword->setTitle('Search');
		$form->search->input->keyword->addSearchField('name,username,email,phone');
				
		$form->search->formWrap('<div style="float:right;margin-bottom: 10px;">','</div>');
		
		$add_sql = $form->search->action();
		$keyword = $form->search->keyword();
		
		echo $form->search->getForm();

		echo $this->_tpl_model->button('admin/user/form?return='.urlencode($this->_tpl_model->_url_current), 'Add User', 'fa fa-plus', '', 'style="margin-right: 10px;"');
		echo $this->_tpl_model->button('admin/user/group?return='.urlencode($this->_tpl_model->_url_current), 'Group', 'fa fa-pencil');

		$form->initRoll($add_sql.' ORDER BY `name` ASC');

		$form->roll->addInput('name', 'sqllinks');
		$form->roll->input->name->setTitle('Name');
		$form->roll->input->name->setLinks('admin/user/form');
		$form->roll->input->name->setModal();
		$form->roll->input->name->setModalReload();
		$form->roll->input->name->setModalLarge();

		$form->roll->addInput('group_ids', 'multiselect');
		$form->roll->input->group_ids->setTitle('Group');
		$form->roll->input->group_ids->setReferenceTable('user_group');
		$form->roll->input->group_ids->setReferenceField('title', 'id');
		$form->roll->input->group_ids->setPlainText();

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

		$form->roll->addInput('location_detail', 'sqlplaintext');
		$form->roll->input->location_detail->setTitle('Address');
		$form->roll->input->location_detail->setFieldName('CONCAT(`address`," ",`location_detail`)');
		$form->roll->input->location_detail->setDisplayColumn();

		$form->roll->addInput('active', 'checkbox');
		$form->roll->input->active->setTitle('Active');
		$form->roll->input->active->setCaption('yes');
		$form->roll->input->active->setDisplayColumn();
		
		$form->roll->addReportAll();
		$form->roll->action();
		echo $form->roll->getForm();
		$this->_tpl_model->show();
	}

	function profile()
	{
		$_GET['id']    = $this->_tpl_model->user['id'];
		$_GET['title'] = 'Profile';
		$this->form();
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
		
		if (isset($_GET['title'])) {
			$form->edit->setHeader($_GET['title']);
		}else{
			$form->edit->setHeader(!empty($id) ? 'Edit User' : 'Add User');
		}
		
		$form->edit->addInput('name','text');
		$form->edit->input->name->setTitle('Name');
		$form->edit->input->name->setRequire();

		if (!$id) {
			$form->edit->addInput('group_ids','multiselect');
			$form->edit->input->group_ids->setTitle('Group');
			$form->edit->input->group_ids->setReferenceTable('user_group');
			$form->edit->input->group_ids->setReferenceField('title', 'id');
			$form->edit->input->group_ids->setRequire();
					
			$form->edit->addInput('username','text');
			$form->edit->input->username->setTitle('Username');
			$form->edit->input->username->setRequire();
			$form->edit->input->username->setUniq();
		
			$form->edit->addInput('password','text');
			$form->edit->input->password->setTitle('Password');
			$form->edit->input->password->setType('password');
			$form->edit->input->password->setRequire();
			$name = $form->edit->input->password->getName();
			if (!empty($_POST[$name])) {
				if ($_POST[$name] != @$_POST['password_re']) {
					$form->edit->input->password->msg = str_replace('{msg}', '<b>Password and Re-Password</b> does not Match', $form->edit->input->password->failMsgTpl);
				}else{
					$this->load->model('_encrypt_model');
					$_POST[$name] = $this->_encrypt_model->encode($_POST[$name]);
				}
			}

			$form->edit->addInput('password_re', 'plaintext');
			$form->edit->input->password_re->setTitle('Re-Password');
			$form->edit->input->password_re->setValue('<input type="password" name="password_re" class="form-control" value="" title="Re-Password" placeholder="Re-Password" required="required">');
		
			$form->edit->addInput('email','text');
			$form->edit->input->email->setTitle('Email');
			$form->edit->input->email->setType('email');
			$form->edit->input->email->setRequire();
			$form->edit->input->email->setUniq();
			
			$form->edit->addInput('phone','text');
			$form->edit->input->phone->setTitle('Phone');
			$form->edit->input->phone->setType('number');
			$form->edit->input->phone->setRequire();
			$form->edit->input->phone->setUniq();
		}else{
			$form->edit->addInput('group_ids', 'multiselect');
			$form->edit->input->group_ids->setTitle('Group');
			$form->edit->input->group_ids->setReferenceTable('user_group');
			$form->edit->input->group_ids->setReferenceField('title', 'id');
			$form->edit->input->group_ids->setPlainText();
			$form->edit->input->group_ids->addTip($this->_tpl_model->button('admin/user/usr?id='.$id.'&act=group_ids&return='.urlencode($this->_tpl_model->_url_current), 'Change', 'fa fa-repeat', 'btn-sm'));

			$form->edit->addInput('username', 'sqlplaintext');
			$form->edit->input->username->setTitle('Username');
			$form->edit->input->username->addTip($this->_tpl_model->button('admin/user/usr?id='.$id.'&return='.urlencode($this->_tpl_model->_url_current), 'Change', 'fa fa-repeat', 'btn-sm'));

			$form->edit->addInput('password', 'plaintext');
			$form->edit->input->password->setTitle('Password');
			$form->edit->input->password->setValue($this->_tpl_model->button('admin/user/pwd?id='.$id.'&return='.urlencode($this->_tpl_model->_url_current), 'Change', 'fa fa-repeat', 'btn-sm'));

			$form->edit->addInput('email', 'sqlplaintext');
			$form->edit->input->email->setTitle('Email');
			$form->edit->input->email->addTip($this->_tpl_model->button('admin/user/usr?id='.$id.'&act=email&return='.urlencode($this->_tpl_model->_url_current), 'Change', 'fa fa-repeat', 'btn-sm'));

			$form->edit->addInput('phone', 'sqlplaintext');
			$form->edit->input->phone->setTitle('Phone');
			$form->edit->input->phone->addTip($this->_tpl_model->button('admin/user/usr?id='.$id.'&act=phone&return='.urlencode($this->_tpl_model->_url_current), 'Change', 'fa fa-repeat', 'btn-sm'));
		}

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
		$form->edit->input->gender->setRequire();

		$form->edit->addInput('birth_date', 'date');
		$form->edit->input->birth_date->setTitle('Birthdate');
		$form->edit->input->birth_date->setRequire();

		$form->edit->addInput('location_data', 'params');
		$form->edit->input->location_data->setTitle('Location');

		$form->edit->input->location_data->addInput('location_input', 'multiinput');
		$form->edit->input->location_data->element->location_input->setTitle('Location');

		$form->edit->input->location_data->element->location_input->addInput('province_id', 'selecttable');
		$form->edit->input->location_data->element->location_input->element->province_id->setTitle('Province');
		$form->edit->input->location_data->element->location_input->element->province_id->setReferenceTable('location');
		$form->edit->input->location_data->element->location_input->element->province_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_data->element->location_input->element->province_id->setReferenceCondition( '`type_id`=1' );
		$form->edit->input->location_data->element->location_input->element->province_id->addOption( '-- Select Province --', '' );
		$form->edit->input->location_data->element->location_input->element->province_id->setRequire();

		$form->edit->input->location_data->element->location_input->addInput('city_id', 'selecttable');
		$form->edit->input->location_data->element->location_input->element->city_id->setTitle('City');
		$form->edit->input->location_data->element->location_input->element->city_id->setReferenceTable('location');
		$form->edit->input->location_data->element->location_input->element->city_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_data->element->location_input->element->city_id->setReferenceCondition( '`type_id`=2' );
		$form->edit->input->location_data->element->location_input->element->city_id->setDependent( $form->edit->input->location_data->element->location_input->element->province_id->getName(), 'par_id' );
		$form->edit->input->location_data->element->location_input->element->city_id->addOption( '-- Select City --', '' );
		$form->edit->input->location_data->element->location_input->element->city_id->setRequire();

		$form->edit->input->location_data->element->location_input->addInput('district_id', 'selecttable');
		$form->edit->input->location_data->element->location_input->element->district_id->setTitle('District');
		$form->edit->input->location_data->element->location_input->element->district_id->setReferenceTable('location');
		$form->edit->input->location_data->element->location_input->element->district_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_data->element->location_input->element->district_id->setReferenceCondition( '`type_id`=3' );
		$form->edit->input->location_data->element->location_input->element->district_id->setDependent( $form->edit->input->location_data->element->location_input->element->city_id->getName(), 'par_id' );
		$form->edit->input->location_data->element->location_input->element->district_id->addOption( '-- Select District --', '' );
		$form->edit->input->location_data->element->location_input->element->district_id->setRequire();

		$form->edit->input->location_data->element->location_input->addInput('village_id', 'selecttable');
		$form->edit->input->location_data->element->location_input->element->village_id->setTitle('Village');
		$form->edit->input->location_data->element->location_input->element->village_id->setReferenceTable('location');
		$form->edit->input->location_data->element->location_input->element->village_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_data->element->location_input->element->village_id->setReferenceCondition( '`type_id`=4' );
		$form->edit->input->location_data->element->location_input->element->village_id->setDependent( $form->edit->input->location_data->element->location_input->element->district_id->getName(), 'par_id' );
		$form->edit->input->location_data->element->location_input->element->village_id->addOption( '-- Select Village --', '' );
		$form->edit->input->location_data->element->location_input->element->village_id->setRequire();

		$form->edit->addInput('address', 'text');
		$form->edit->input->address->setTitle('Address');
		$form->edit->input->address->setRequire();

		$form->edit->addInput('active', 'checkbox');
		$form->edit->input->active->setTitle('Active');
		$form->edit->input->active->setCaption('yes');
		
		$form->edit->onSave(function($id, $f)
		{
			$data = $f->db->getRow('SELECT * FROM `user` WHERE `id`='.$id);
			if ($data) {
				$data_update = array();
				if ($data['location_data']) {
					$data['location_data'] = @(array)json_decode($data['location_data'], 1);
					if (!empty($data['location_data']['village_id'])) {
						$data_update['location_id'] = $data['location_data']['village_id'];
						$location                   = $f->db->getRow('SELECT `title`,`detail` FROM `location` WHERE `id`='.$data_update['location_id']);
						if ($location) {
							$data_update['location_title']  = $location['title'];
							$data_update['location_detail'] = $location['detail'];
						}
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
			$form->edit->input->password->setType('password');
			$form->edit->input->password->setRequire();
			$name = $form->edit->input->password->getName();
			if (!empty($_POST[$name])) {
				if ($_POST[$name] != @$_POST['password_re']) {
					$form->edit->input->password->msg = str_replace('{msg}', '<b>Password and Re-Password</b> does not Match', $form->edit->input->password->failMsgTpl);
				}else{
					$this->load->model('_encrypt_model');
					$_POST[$name] = $this->_encrypt_model->encode($_POST[$name]);
				}
			}

			$form->edit->addInput('password_re', 'plaintext');
			$form->edit->input->password_re->setTitle('Re-Password');
			$form->edit->input->password_re->setValue('<input type="password" name="password_re" class="form-control" value="" title="Re-Password" placeholder="Re-Password" required="required">');

			$form->edit->action();
			$form->edit->input->password->setValue('');
			echo $form->edit->getForm();
			$this->_tpl_model->show();
		}
	}

	function usr()
	{
		$id = @intval($_GET['id']);
		$this->_tpl_model->setLayout('blank');
		if ($id) {
			$form = $this->_pea_model->newForm('user');
			
			$form->initEdit('WHERE `id`='.$id);

			$form->edit->addInput('name', 'sqlplaintext');
			$form->edit->input->name->setTitle('Name');

			switch (@$_GET['act']) {
				case 'group_ids':
					$form->edit->setHeader('Change Group');
					
					$form->edit->addInput('group_ids','multiselect');
					$form->edit->input->group_ids->setTitle('Group');
					$form->edit->input->group_ids->setReferenceTable('user_group');
					$form->edit->input->group_ids->setReferenceField('title', 'id');
					$form->edit->input->group_ids->setRequire();
					break;
				case 'email':
					$form->edit->setHeader('Change Email');
					
					$form->edit->addInput('email','text');
					$form->edit->input->email->setTitle('Email');
					$form->edit->input->email->setType('email');
					$form->edit->input->email->setRequire();
					$form->edit->input->email->setUniq();
					break;

				case 'phone':
					$form->edit->setHeader('Change Phone');
					
					$form->edit->addInput('phone','text');
					$form->edit->input->phone->setTitle('Phone');
					$form->edit->input->phone->setType('number');
					$form->edit->input->phone->setRequire();
					$form->edit->input->phone->setUniq();
					break;		
				
				default:
					$form->edit->setHeader('Change Username');
					
					$form->edit->addInput('username','text');
					$form->edit->input->username->setTitle('Username');
					$form->edit->input->username->setRequire();
					$form->edit->input->username->setUniq();
					break;
			}

			$form->edit->action();
			echo $form->edit->getForm();
			$this->_tpl_model->show();
		}
	}

	function group()
	{
		echo $this->_tpl_model->button('admin/user/group_form?return='.urlencode($this->_tpl_model->_url_current), 'Add Group', 'fa fa-plus', '', 'style="margin-bottom: 5px;"');

		$form = $this->_pea_model->newForm('user_group');
	
		$form->initRoll('WHERE 1 ORDER BY `id` ASC');

		$form->roll->addInput('title', 'sqllinks');
		$form->roll->input->title->setTitle('Title');
		$form->roll->input->title->setLinks('admin/user/group_form');

		$form->roll->addInput('type', 'select');
		$form->roll->input->type->setTitle('Type');
		$form->roll->input->type->addOptions($this->user_group_type);
		$form->roll->input->type->setPlainText();
		
		$form->roll->setSaveTool(false);
		$form->roll->action();
		echo $form->roll->getForm();
		$this->_tpl_model->show();
	}
	function group_form()
	{
		$id   = @intval($_GET['id']);
		$form = $this->_pea_model->newForm('user_group');

		$form->initEdit(!empty($id) ? 'WHERE `id`='.$id : '');

		$form->edit->setHeader(!empty($id) ? 'Edit Group' : 'Add Group');

		$form->edit->addInput('title', 'text');
		$form->edit->input->title->setTitle('Title');

		$form->edit->addInput('type', 'select');
		$form->edit->input->type->setTitle('Type');
		$form->edit->input->type->addOptions($this->user_group_type);
		
		$form->edit->action();
		echo $form->edit->getForm();
		$this->_tpl_model->show();
	}

	function login()
	{
		if ($this->router->uri->uri_string != $this->_tpl_model->config('dashboard', 'login_uri')) {
			show_404();
		}
		$this->load->model('_encrypt_model');
		$input = array(
			'usr' => mt_rand(100000000000,500000000000),
			'pwd' => mt_rand(500000000001,900000000000),
			'msg' => '',
		);
		if (!empty($_POST['token'])) {
			$token = $this->_encrypt_model->decodeToken($_POST['token']);
			if ($token) {
				$token = explode('|', $token);
				if (!empty($_POST[$token[0]]) and !empty($_POST[$token[1]])) {
					if ($this->_tpl_model->user_login($_POST[$token[0]], $_POST[$token[1]], 1)) {
						redirect($this->_tpl_model->_url.'admin/dashboard');
					}else{
						$input['msg'] = $this->_tpl_model->msg($this->_tpl_model->user_msg(), 'danger');
					}
				}
			}else{
				$input['msg'] = $this->_tpl_model->msg('Token Expired', 'danger');
			}
		}
		$input['token'] = $this->_encrypt_model->encodeToken($input['usr'].'|'.$input['pwd'], 2);

		$this->_tpl_model->setLayout('blank');
		$this->_tpl_model->view('User/login', ['input' => $input]);
		$this->_tpl_model->show();
	}
	function logout()
	{
		$this->_tpl_model->user_logout();
		redirect($this->_tpl_model->_url);
	}
}

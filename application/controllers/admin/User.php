<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller 
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('_pea_model');
		$this->load->model('_tpl_model');

		$this->_tpl_model->menu_unprotect('profile');
		$this->_tpl_model->menu_unprotect('usr');
		$this->_tpl_model->menu_unprotect('pwd');
		$this->_tpl_model->menu_unprotect('notif');
		$this->_tpl_model->menu_unprotect('address');
		$this->_tpl_model->menu_unprotect('address_form');

		if (!in_array($this->_tpl_model->method, ['login','logout'])) {
			$this->_tpl_model->user_login_validate(1);
		}

		$this->_tpl_model->setTemplate('admin');
		$this->_tpl_model->nav_add('admin/dashboard/main', '<i class="fa fa-home"></i> Home', '0');
	}

	function index()
	{
		$form = $this->_pea_model->newForm('user');

		$form->initSearch();

		$form->search->addInput('group_ids', 'selecttable');
		$form->search->input->group_ids->setTitle('Group');
		$form->search->input->group_ids->setReferenceTable('user_group');
		$form->search->input->group_ids->setReferenceField('title', 'id');
		$form->search->input->group_ids->addOption('-- Select Group --', '');
		$form->search->input->group_ids->setSearchFunction(function($value='')
		{
			return '`group_ids` LIKE "%\"'.$value.'\"%"';
		});
		if ($this->_tpl_model->user['id'] != 1) {
			$form->search->input->group_ids->setReferenceCondition('id != 1');
		}

		$form->search->addInput('keyword', 'keyword');
		$form->search->input->keyword->setTitle('Search');
		$form->search->input->keyword->addSearchField('name,username,email,phone,province_title,city_title,district_title,village_title,address,zip_code');
				
		$add_sql = $form->search->action();
		$keyword = $form->search->keyword();
		
		echo $form->search->getForm();

		echo $this->_tpl_model->button('admin/user/form_add', 'Add User', 'fa fa-plus', 'modal_reload', 'style="margin-right: 10px; margin-bottom: 15px;width: 100px;"', 1);
		
		if ($this->_tpl_model->user['id'] == 1) {
			echo $this->_tpl_model->button('admin/user/group?return='.urlencode($this->_tpl_model->_url_current), 'Group', 'fa fa-pencil', '', 'style="margin-right: 10px; margin-bottom: 15px;width: 100px;"');
		}

		$form->initRoll($add_sql.' ORDER BY `id` DESC');

		$form->roll->addInput('name', 'sqllinks');
		$form->roll->input->name->setTitle('Name');
		$form->roll->input->name->setLinks('admin/user/form');
		$form->roll->input->name->setModal();
		$form->roll->input->name->setModalReload();
		$form->roll->input->name->setDisplayColumn();

		$form->roll->addInput('group_ids', 'multiselect');
		$form->roll->input->group_ids->setTitle('Group');
		$form->roll->input->group_ids->setReferenceTable('user_group');
		$form->roll->input->group_ids->setReferenceField('title', 'id');
		$form->roll->input->group_ids->setPlainText();
		$form->roll->input->group_ids->setDisplayColumn();

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
		$form->roll->input->gender->addOption('Other', 3);
		$form->roll->input->gender->setPlainText();
		$form->roll->input->gender->setDisplayColumn(false);

		$form->roll->addInput('birth_place', 'sqlplaintext');
		$form->roll->input->birth_place->setTitle('Birthplace');
		$form->roll->input->birth_place->setDisplayColumn(false);

		$form->roll->addInput('birth_date', 'date');
		$form->roll->input->birth_date->setTitle('Birthdate');
		$form->roll->input->birth_date->setDateFormat('d M Y');
		$form->roll->input->birth_date->setPlainText();
		$form->roll->input->birth_date->setDisplayColumn(false);

		$form->roll->addInput('location_detail', 'sqlplaintext');
		$form->roll->input->location_detail->setTitle('Address');
		$form->roll->input->location_detail->setFieldName('CONCAT(`address`," ",`village_title`,", ",`district_title`,", ",`city_title`,", ",`province_title`,", ",`zip_code`)');
		$form->roll->input->location_detail->setDisplayColumn();

		$form->roll->addInput('login_wrap', 'multiinput');
		$form->roll->input->login_wrap->setTitle('Login');
		$form->roll->input->login_wrap->setDisplayColumn();
		$form->roll->input->login_wrap->setDelimiter('&nbsp;&nbsp;&nbsp;');

		$form->roll->input->login_wrap->addInput('login_act', 'editlinks');
		$form->roll->input->login_wrap->element->login_act->setTitle('<i class="fa fa-sign-in" title="Login with this account"></i>');
		$form->roll->input->login_wrap->element->login_act->setLinks('admin/user/login_auto');
		$form->roll->input->login_wrap->element->login_act->addAttr('target="_BLANK"');
		$form->roll->input->login_wrap->element->login_act->addAttr('onclick="return confirm(\'Login now ?\')"');

		$form->roll->input->login_wrap->addInput('login_log', 'editlinks');
		$form->roll->input->login_wrap->element->login_log->setTitle('<i class="fa fa-clock" title="Login History"></i>');
		$form->roll->input->login_wrap->element->login_log->setLinks('admin/user/log');

		$form->roll->addInput('active', 'checkbox');
		$form->roll->input->active->setTitle('Active');
		$form->roll->input->active->setCaption('yes');
		$form->roll->input->active->setDisplayColumn();

		$form->roll->addInput('created', 'datetime');
		$form->roll->input->created->setTitle('Created');
		$form->roll->input->created->setPlainText();
		$form->roll->input->created->setDisplayColumn();

		$form->roll->addInput('updated', 'datetime');
		$form->roll->input->updated->setTitle('Updated');
		$form->roll->input->updated->setPlainText();
		$form->roll->input->updated->setDisplayColumn(false);
		
		$form->roll->setDeleteCondition('{roll_id} == '.$this->_tpl_model->user['id']);
		$form->roll->setDeleteCondition('{roll_id} == 1');

		$form->roll->onDelete(function($id, $f)
		{
			lib_path_delete($f->_root.'files/user_field/'.$id);
		});
		$form->roll->onSave(function($id, $f)
		{
			if ($id == 1) {
				if (!$f->db->getOne('SELECT `active` FROM `user` WHERE `id`=1')) {
					$f->db->update('user', array('active' => 1), 1);
				}
			}
		});
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

	function form_add()
	{
		$_GET['return'] = '';
		$this->_tpl_model->setLayout('blank');

		$form = $this->_pea_model->newForm('user');
		$form->initEdit();

		$form->edit->setHeader('Select Group');
		$form->edit->setModalResponsive();
		
		$form->edit->addInput('group_id','selecttable');
		$form->edit->input->group_id->setTitle('');
		$form->edit->input->group_id->setReferenceTable('user_group');
		$form->edit->input->group_id->setReferenceField('title', 'id');
		$form->edit->input->group_id->setRequire();
		if ($this->_tpl_model->user['id'] != 1) {
			$form->edit->input->group_id->setReferenceCondition('id != 1');
		}
		$group_id = $form->edit->input->group_id->getName();
		if (isset($_POST[$group_id])) {
			redirect('admin/user/form?group_id='.$_POST[$group_id]);
		}
				
		$form->edit->setSaveButton('<i class="fa fa-send"></i> Next');
		$form->edit->action();
		echo $form->edit->getForm();
		$this->_tpl_model->show();
	}

	function form()
	{
		$id   = @intval($_GET['id']);
		$form = $this->_pea_model->newForm('user');

		$_GET['return'] = '';
		$this->_tpl_model->setLayout('blank');

		$form->initEdit(!empty($id) ? 'WHERE `id`='.$id : '');
		
		if (isset($_GET['title'])) {
			$form->edit->setHeader($_GET['title']);
		}else{
			$form->edit->setHeader(!empty($id) ? 'Edit User' : 'Add User');
		}
		$form->edit->setModalResponsive();
		
		$form->edit->addInput('name','text');
		$form->edit->input->name->setTitle('Name');
		$form->edit->input->name->setRequire();

		if (!$id) {
			$group_id    = @intval($_GET['group_id']);
			$group_title = $this->_db_model->getOne('SELECT `title` FROM `user_group` WHERE `id`='.$group_id);
			if (!$group_title) {
				redirect('admin/user/form_add');
			}
			$_GET['return'] = $this->_tpl_model->_url.'admin/user/form_add';

			$form->edit->addExtraField('group_ids', '["'.$group_id.'"]');

			$form->edit->addInput('group_title', 'plaintext');
			$form->edit->input->group_title->setTitle('Group');
			$form->edit->input->group_title->setValue($group_title);
					
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
			$form->edit->input->phone->setType('tel');
			$form->edit->input->phone->setRequire();
			$form->edit->input->phone->setUniq();
		}else{
			$form->edit->addInput('group_ids', 'multiselect');
			$form->edit->input->group_ids->setTitle('Group');
			$form->edit->input->group_ids->setReferenceTable('user_group');
			$form->edit->input->group_ids->setReferenceField('title', 'id');
			$form->edit->input->group_ids->setPlainText();

			$form->edit->addInput('username', 'sqlplaintext');
			$form->edit->input->username->setTitle('Username');

			$form->edit->addInput('password', 'plaintext');
			$form->edit->input->password->setTitle('Password');

			$form->edit->addInput('email', 'sqlplaintext');
			$form->edit->input->email->setTitle('Email');

			$form->edit->addInput('phone', 'sqlplaintext');
			$form->edit->input->phone->setTitle('Phone');

			if ($this->_tpl_model->method == 'profile') {
				$form->edit->input->group_ids->addTip($this->_tpl_model->button('admin/user/usr?id='.$id.'&act=group_ids', 'Change', 'fa fa-repeat', 'btn-sm modal_reload', '', 1));
				$form->edit->input->username->addTip($this->_tpl_model->button('admin/user/usr?id='.$id.'', 'Change', 'fa fa-repeat', 'btn-sm modal_reload', '', 1));
				$form->edit->input->password->setValue($this->_tpl_model->button('admin/user/pwd?id='.$id.'', 'Change', 'fa fa-repeat', 'btn-sm modal_reload', '', 1));
				$form->edit->input->email->addTip($this->_tpl_model->button('admin/user/usr?id='.$id.'&act=email', 'Change', 'fa fa-repeat', 'btn-sm modal_reload', '', 1));
				$form->edit->input->phone->addTip($this->_tpl_model->button('admin/user/usr?id='.$id.'&act=phone', 'Change', 'fa fa-repeat', 'btn-sm modal_reload', '', 1));
			}else{
				$form->edit->input->group_ids->addTip($this->_tpl_model->button('admin/user/usr?id='.$id.'&act=group_ids&return='.urlencode($this->_tpl_model->_url_current), 'Change', 'fa fa-repeat', 'btn-sm'));
				$form->edit->input->username->addTip($this->_tpl_model->button('admin/user/usr?id='.$id.'&return='.urlencode($this->_tpl_model->_url_current), 'Change', 'fa fa-repeat', 'btn-sm'));
				$form->edit->input->password->setValue($this->_tpl_model->button('admin/user/pwd?id='.$id.'&return='.urlencode($this->_tpl_model->_url_current), 'Change', 'fa fa-repeat', 'btn-sm'));
				$form->edit->input->email->addTip($this->_tpl_model->button('admin/user/usr?id='.$id.'&act=email&return='.urlencode($this->_tpl_model->_url_current), 'Change', 'fa fa-repeat', 'btn-sm'));
				$form->edit->input->phone->addTip($this->_tpl_model->button('admin/user/usr?id='.$id.'&act=phone&return='.urlencode($this->_tpl_model->_url_current), 'Change', 'fa fa-repeat', 'btn-sm'));
			}

			$telegram_conf = $this->_tpl_model->config('telegram');
			if (!empty($telegram_conf['active'])) {
				$form->edit->addInput('telegram_data', 'sqlplaintext');
				$form->edit->input->telegram_data->setTitle('Telegram');
				$form->edit->input->telegram_data->setDisplayFunction(function($value='')
				{
					$value = json_decode($value, 1);
					if ($value) {
						return '@'.$value['username'].' '.$value['first_name'].' '.$value['last_name'];
					}
				});
				if ($this->_tpl_model->method == 'profile') {
					$form->edit->input->telegram_data->addTip($this->_tpl_model->button('admin/user/usr?id='.$id.'&act=telegram', 'Connect', 'fa fa-link', 'btn-sm modal_reload', '', 1));
				}else{
					$form->edit->input->telegram_data->addTip($this->_tpl_model->button('admin/user/usr?id='.$id.'&act=telegram&return='.urlencode($this->_tpl_model->_url_current), 'Connect', 'fa fa-link', 'btn-sm'));
				}
			}
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
		$form->edit->input->gender->addOption('Other', 3);
		$form->edit->input->gender->setRequire();

		$form->edit->addInput('birth_place', 'selecttable');
		$form->edit->input->birth_place->setTitle('Birthplace');
		$form->edit->input->birth_place->setReferenceTable('location');
		$form->edit->input->birth_place->setReferenceField( 'title', 'title' );
		$form->edit->input->birth_place->setReferenceCondition( '`type_id`=3' );
		$form->edit->input->birth_place->setReferenceOrderBy( 'title' );
		$form->edit->input->birth_place->setAutoComplete();
		$form->edit->input->birth_place->setRequire();

		$form->edit->addInput('birth_date', 'date');
		$form->edit->input->birth_date->setTitle('Birthdate');
		$form->edit->input->birth_date->setRequire();
		$form->edit->input->birth_date->setMaxDate('-7 YEARS');

		$form->edit->addInput('location_input', 'multiinput');
		$form->edit->input->location_input->setTitle('Location');

		$form->edit->input->location_input->addInput('province_id', 'selecttable');
		$form->edit->input->location_input->element->province_id->setTitle('Province');
		$form->edit->input->location_input->element->province_id->setReferenceTable('location');
		$form->edit->input->location_input->element->province_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_input->element->province_id->setReferenceCondition( '`type_id`=1' );
		$form->edit->input->location_input->element->province_id->setReferenceOrderBy( 'title' );
		$form->edit->input->location_input->element->province_id->addOption( '-- Select Province --', '' );
		$form->edit->input->location_input->element->province_id->setRequire();

		$form->edit->input->location_input->addInput('city_id', 'selecttable');
		$form->edit->input->location_input->element->city_id->setTitle('City');
		$form->edit->input->location_input->element->city_id->setReferenceTable('location');
		$form->edit->input->location_input->element->city_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_input->element->city_id->setReferenceCondition( '`type_id`=2' );
		$form->edit->input->location_input->element->city_id->setReferenceOrderBy( 'title' );
		$form->edit->input->location_input->element->city_id->setDependent( $form->edit->input->location_input->element->province_id->getName(), 'par_id' );
		$form->edit->input->location_input->element->city_id->addOption( '-- Select City --', '' );
		$form->edit->input->location_input->element->city_id->setRequire();

		$form->edit->input->location_input->addInput('district_id', 'selecttable');
		$form->edit->input->location_input->element->district_id->setTitle('District');
		$form->edit->input->location_input->element->district_id->setReferenceTable('location');
		$form->edit->input->location_input->element->district_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_input->element->district_id->setReferenceCondition( '`type_id`=3' );
		$form->edit->input->location_input->element->district_id->setReferenceOrderBy( 'title' );
		$form->edit->input->location_input->element->district_id->setDependent( $form->edit->input->location_input->element->city_id->getName(), 'par_id' );
		$form->edit->input->location_input->element->district_id->addOption( '-- Select District --', '' );
		$form->edit->input->location_input->element->district_id->setRequire();

		$form->edit->input->location_input->addInput('village_id', 'selecttable');
		$form->edit->input->location_input->element->village_id->setTitle('Village');
		$form->edit->input->location_input->element->village_id->setReferenceTable('location');
		$form->edit->input->location_input->element->village_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_input->element->village_id->setReferenceCondition( '`type_id`=4' );
		$form->edit->input->location_input->element->village_id->setReferenceOrderBy( 'title' );
		$form->edit->input->location_input->element->village_id->setDependent( $form->edit->input->location_input->element->district_id->getName(), 'par_id' );
		$form->edit->input->location_input->element->village_id->addOption( '-- Select Village --', '' );
		$form->edit->input->location_input->element->village_id->setRequire();
			
		$form->edit->addInput('zip_code','text');
		$form->edit->input->zip_code->setTitle('Zip Code');
		$form->edit->input->zip_code->setType('number');
		$form->edit->input->zip_code->setRequire();

		$form->edit->addInput('address', 'text');
		$form->edit->input->address->setTitle('Address');
		$form->edit->input->address->setRequire();
		$form->edit->input->address->addTip('Jln. Jendral Sudirman No.123 RT.05 RW.06');

		if ($id) {
			$group_ids = @(array)json_decode($this->_db_model->getOne('SELECT `group_ids` FROM `user` WHERE `id`='.$id), 1);
		}else{
			$group_ids = [$group_id];
		}
		if ($group_ids) {
			$global_field = $this->_db_model->getOne('SELECT 1 FROM `user_group` WHERE `id` IN('.implode(',',$group_ids).') AND `global_field`=1 LIMIT 1');
			if ($global_field) {
				$group_ids[] = 0;
			}
			
			$form->edit->addInput('params', 'params');
			$form->edit->input->params->setTitle('');

			$fields = $this->_db_model->getAll('SELECT `name`,`form`,`title`,`required`,`params` FROM `user_field` WHERE `group_id` IN('.implode(',',$group_ids).') ORDER BY `group_id`,`orderby`');
			$form->edit->input->params->setParams($fields);
		}

		$form->edit->addInput('active', 'checkbox');
		$form->edit->input->active->setTitle('Active');
		$form->edit->input->active->setCaption('yes');
		if ($id == 1) {
			$form->edit->input->active->setPlainText();
			if ($this->_tpl_model->user['id'] != 1) {
				$form->edit->input->name->setPlainText();
				$form->edit->input->image->setPlainText();
				$form->edit->input->gender->setPlainText();
				$form->edit->input->birth_date->setPlainText();
				$form->edit->input->location_input->element->province_id->setPlainText();
				$form->edit->input->location_input->element->city_id->setPlainText();
				$form->edit->input->location_input->element->district_id->setPlainText();
				$form->edit->input->location_input->element->village_id->setPlainText();
				$form->edit->input->address->setPlainText();
				$form->edit->input->address->setTip('');

				$form->edit->input->group_ids->setTip('');
				$form->edit->input->username->setTip('');
				$form->edit->input->email->setTip('');
				$form->edit->input->phone->setTip('');
				$form->edit->input->telegram_data->setTip('');

				$form->edit->input->password->setInputPosition('hidden');
				
				$form->edit->setSaveTool(false);
			}
		}
		if ($this->_tpl_model->user['id'] != 1) {
			$form->edit->input->group_ids->setTip('');
		}

		if ($this->_tpl_model->method == 'profile') {
			$form->edit->input->address->addTip('<br>'.$this->_tpl_model->button('admin/user/address', 'Manage Address List', 'far fa-address-book', 'modal_reload modal_large', '', 1));
			$form->edit->input->active->addTip($this->_tpl_model->button('admin/user/log?id='.$this->_tpl_model->user['id'].'&return='.urlencode($this->_tpl_model->_url_current), 'Manage Active Sessions', 'far fa-user-shield', '', '', 0));
		}
		
		$form->edit->onSave(function($id, $f)
		{
			$data = $f->db->getRow('SELECT * FROM `user` WHERE `id`='.$id);
			if ($data) {
				$data_update = array(
					'province_title' => $f->db->getOne('SELECT `title` FROM `location` WHERE `id`='.$data['province_id']),
					'city_title'     => $f->db->getOne('SELECT `title` FROM `location` WHERE `id`='.$data['city_id']),
					'district_title' => $f->db->getOne('SELECT `title` FROM `location` WHERE `id`='.$data['district_id']),
					'village_title'  => $f->db->getOne('SELECT `title` FROM `location` WHERE `id`='.$data['village_id']),
				);
				$data_address                = $data_update;
				$data_address['province_id'] = $data['province_id'];
				$data_address['city_id']     = $data['city_id'];
				$data_address['district_id'] = $data['district_id'];
				$data_address['village_id']  = $data['village_id'];
				$data_address['zip_code']    = $data['zip_code'];
				$data_address['address']     = $data['address'];
				$data_address['email']       = $data['email'];
				$data_address['phone']       = $data['phone'];
				$data_address['title']       = 'Utama';

				$address_id = $f->db->getOne('SELECT `id` FROM `user_address` WHERE `user_id`='.$id.' AND `main`=1');
				if ($address_id) {
					$f->db->update('user_address', $data_address, $address_id);
				}else{
					$data_address['user_id'] = $id;
					$data_address['main']    = 1;
					$f->db->insert('user_address', $data_address);
				}

				$f->db->update('user', $data_update, $id);
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
			$form->edit->setModalResponsive();

			$form->edit->addInput('name', 'sqlplaintext');
			$form->edit->input->name->setTitle('Name');

			$form->edit->addInput('username', 'sqlplaintext');
			$form->edit->input->username->setTitle('Username');

			if ($id == 1 and $id != $this->_tpl_model->user['id']) {
				echo $this->_tpl_model->msg('Not Allowed', 'danger');
				$form->edit->setSaveTool(false);
			}else{
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
			}			
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
			
			$form->edit->setModalResponsive();

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

					if ($id == 1 or $this->_tpl_model->user['id'] != 1) {
						$form->edit->input->group_ids->setPlainText();
						$form->edit->setSaveTool(false);
					}
					break;
				case 'email':
					$form->edit->setHeader('Change Email');
					
					$form->edit->addInput('email','text');
					$form->edit->input->email->setTitle('Email');
					$form->edit->input->email->setType('email');
					$form->edit->input->email->setRequire();
					$form->edit->input->email->setUniq();

					if ($id == 1 and $id != $this->_tpl_model->user['id']) {
						$form->edit->input->email->setPlainText();
						$form->edit->setSaveTool(false);
					}
					break;

				case 'phone':
					$form->edit->setHeader('Change Phone');
					
					$form->edit->addInput('phone','text');
					$form->edit->input->phone->setTitle('Phone');
					$form->edit->input->phone->setType('tel');
					$form->edit->input->phone->setRequire();
					$form->edit->input->phone->setUniq();

					if ($id == 1 and $id != $this->_tpl_model->user['id']) {
						$form->edit->input->phone->setPlainText();
						$form->edit->setSaveTool(false);
					}
					break;		

				case 'telegram':
					$form->edit->setHeader('Connect Telegram Account');
					
					$form->edit->addInput('telegram_id','hidden');
					$form->edit->input->telegram_id->addAttr('id="telegram_id"');
					
					$form->edit->addInput('telegram_data','hidden');
					$form->edit->input->telegram_data->addAttr('id="telegram_data"');

					$telegram_conf = $this->_tpl_model->config('telegram');
					
					if ($id == $this->_tpl_model->user['id'] and !empty($telegram_conf['active'])) {
						$telegram_conf['data'] = @(array)json_decode($telegram_conf['data']);

						if (!empty($telegram_conf['data']['username'])) {
							$this->_tpl_model->js('controllers/admin/user_usr_telegram.js');
							$code = time().mt_rand();

							$form->edit->addInput('telegram_input','plaintext');
							$form->edit->input->telegram_input->setTitle('Telegram Account');
							$form->edit->input->telegram_input->setValue('<input id="telegram_input" type="text" class="form-control" value="" title="Telegram Account" placeholder="Telegram Account" data-code="'.$code.'" data-url="https://api.telegram.org/bot'.$telegram_conf['token'].'/getUpdates" readonly>');

							$form->edit->addInput('telegram_instructions','plaintext');
							$form->edit->input->telegram_instructions->setTitle('Instructions');
							$form->edit->input->telegram_instructions->setValue('<ol><li>Click <a href="https://t.me/'.$telegram_conf['data']['username'].'?start='.$code.'" target="_BLANK">Here</a> to open Our Telegram Account.</li><li>Click <b>Start</b> on the message box.</li><li>Wait a second and see Your Telegram Account appear on the top.</li><li>Then Save it !</li></ol>');
						}
					}else{
						$form->edit->setSaveTool(false);
					}
					break;		
				
				default:
					$form->edit->setHeader('Change Username');
					
					$form->edit->addInput('username','text');
					$form->edit->input->username->setTitle('Username');
					$form->edit->input->username->setRequire();
					$form->edit->input->username->setUniq();

					if ($id == 1 and $id != $this->_tpl_model->user['id']) {
						$form->edit->input->username->setPlainText();
						$form->edit->setSaveTool(false);
					}
					break;
			}

			$form->edit->action();
			echo $form->edit->getForm();
			$this->_tpl_model->show();
		}
	}

	function group()
	{
		echo $this->_tpl_model->button('admin/user/group_form', 'Add Group', 'fa fa-plus', 'modal_reload', 'style="margin-bottom: 15px;"', 1);
		echo $this->_tpl_model->button('admin/user/group_field?return='.urlencode($this->_tpl_model->_url_current), 'Global Field', 'fa fa-pencil', '', 'style="margin-bottom: 15px;margin-left: 15px;"', 0);

		$form = $this->_pea_model->newForm('user_group');
	
		$form->initRoll('WHERE 1 ORDER BY `id` ASC');

		$form->roll->addInput('title', 'sqllinks');
		$form->roll->input->title->setTitle('Title');
		$form->roll->input->title->setLinks('admin/user/group_form');
		$form->roll->input->title->setModal();
		$form->roll->input->title->setModalReload();
		$form->roll->input->title->setDisplayColumn();

		$form->roll->addInput('type', 'select');
		$form->roll->input->type->setTitle('Type');
		$form->roll->input->type->addOptions($this->_tpl_model->user_group_type);
		$form->roll->input->type->setPlainText();
		$form->roll->input->type->setDisplayColumn();

		$form->roll->addInput('menu_ids', 'multiselect');
		$form->roll->input->menu_ids->setTitle('Menu');
		$form->roll->input->menu_ids->setReferenceTable('menu');
		$form->roll->input->menu_ids->setReferenceField('title','id');
		$form->roll->input->menu_ids->setReferenceCondition('`protect`=1');
		$form->roll->input->menu_ids->setReferenceNested('par_id');
		$form->roll->input->menu_ids->setReferenceOrderBy('orderby');
		$form->roll->input->menu_ids->addAttr('size="10"');
		$form->roll->input->menu_ids->addOption('All Menu', 'all');
		$form->roll->input->menu_ids->setDelimiter('<br>');
		$form->roll->input->menu_ids->setDelimiterAlt(' , ');
		$form->roll->input->menu_ids->setPlainText();
		$form->roll->input->menu_ids->setDisplayColumn();

		$form->roll->addInput('approval', 'select');
		$form->roll->input->approval->setTitle('Registration Approval');
		$form->roll->input->approval->addOption('Registration Closed', '0');
		$form->roll->input->approval->addOption('Direct Approve', '1');
		$form->roll->input->approval->addOption('Manual Approve', '2');
		$form->roll->input->approval->setPlainText();
		$form->roll->input->approval->setDisplayColumn();
		$form->roll->input->approval->setDisplayFunction(function($value='', $id=0, $index='', $values=array())
		{
			if ($values[$index]['approval']) {
				$value .= ' - <a href="'.$this->_tpl_model->_url.'user/register?id='.$id.'" target="_BLANK" title="Goto register form"><i class="far fa-share-square"></i></a>';
			}
			return $value;
		});

		$form->roll->addInput('global_field', 'checkbox');
		$form->roll->input->global_field->setTitle('Global Field');
		$form->roll->input->global_field->setCaption('Yes');
		$form->roll->input->global_field->setPlainText();
		$form->roll->input->global_field->setDisplayColumn();

		$form->roll->addInput('field', 'editlinks');
		$form->roll->input->field->setTitle('Custom Field');
		$form->roll->input->field->setCaption('Manage');
		$form->roll->input->field->setLinks('admin/user/group_field');
		$form->roll->input->field->setDisplayColumn();

		$form->roll->addInput('created', 'datetime');
		$form->roll->input->created->setTitle('Created');
		$form->roll->input->created->setPlainText();
		$form->roll->input->created->setDisplayColumn();

		$form->roll->addInput('updated', 'datetime');
		$form->roll->input->updated->setTitle('Updated');
		$form->roll->input->updated->setPlainText();
		$form->roll->input->updated->setDisplayColumn(false);

		$form->roll->onDelete(function($id, $f)
		{
			$f->db->delete('user_field', '`group_id`='.$id);
		});
		
		$form->roll->setDeleteCondition('{roll_id}==1');
		$form->roll->setSaveTool(false);
		$form->roll->addReportAll();
		$form->roll->action();
		echo $form->roll->getForm();
		$this->_tpl_model->show();
	}
	function group_form()
	{
		$id   = @intval($_GET['id']);
		$form = $this->_pea_model->newForm('user_group');

		$_GET['return'] = '';
		$this->_tpl_model->setLayout('blank');
		
		$form->initEdit(!empty($id) ? 'WHERE `id`='.$id : '');

		$form->edit->setHeader(!empty($id) ? 'Edit Group' : 'Add Group');
		$form->edit->setModalResponsive();

		$form->edit->addInput('title', 'text');
		$form->edit->input->title->setTitle('Title');
		$form->edit->input->title->setRequire();

		$form->edit->addInput('type', 'select');
		$form->edit->input->type->setTitle('Type');
		$form->edit->input->type->addOptions($this->_tpl_model->user_group_type);

		$form->edit->addInput('menu_ids', 'multiselect');
		$form->edit->input->menu_ids->setTitle('Menu');
		$form->edit->input->menu_ids->setReferenceTable('menu');
		$form->edit->input->menu_ids->setReferenceField('title','id');
		$form->edit->input->menu_ids->setReferenceCondition('`protect`=1');
		$form->edit->input->menu_ids->setReferenceNested('par_id');
		$form->edit->input->menu_ids->setReferenceOrderBy('orderby');
		$form->edit->input->menu_ids->setDependent($form->edit->input->type->getName(), 'type');
		$form->edit->input->menu_ids->addAttr('size="10"');
		$form->edit->input->menu_ids->addOption('All Menu', 'all');

		$form->edit->addInput('approval', 'select');
		$form->edit->input->approval->setTitle('Registration Approval');
		$form->edit->input->approval->addOption('Registration Closed', '0');
		$form->edit->input->approval->addOption('Direct Approve', '1');
		$form->edit->input->approval->addOption('Manual Approve', '2');

		$form->edit->addInput('global_field', 'checkbox');
		$form->edit->input->global_field->setTitle('Include Global Field');
		$form->edit->input->global_field->setCaption('Yes');

		if ($id == 1) {
			$form->edit->input->type->setPlainText();
			$form->edit->input->menu_ids->setDelimiter('<br>');
			$form->edit->input->menu_ids->setPlainText();
			$form->edit->input->approval->setPlainText();
		}
		
		$form->edit->action();
		echo $form->edit->getForm();
		$this->_tpl_model->show();
	}

	function group_field()
	{
		$id = @intval($_GET['id']);
		if ($id) {
			$group_title = $this->_tpl_model->_db_model->getOne('SELECT `title` FROM `user_group` WHERE `id`='.$id);
			if ($group_title) {
				$this->_tpl_model->nav_add('admin/user/group_field?id='.$id, 'Custom Field');
				$this->_tpl_model->nav_add($group_title);
			}
		}else{
			$this->_tpl_model->nav_add('Global Field');
		}
		echo $this->_tpl_model->button('admin/user/group_field_form?group_id='.$id, 'Add Field', 'fa fa-plus', 'modal_reload modal_large', 'style="margin-bottom: 15px;"', 1);

		$form = $this->_pea_model->newForm('user_field');
	
		$form->initRoll('WHERE `group_id`='.$id.' ORDER BY `orderby` ASC');

		$form->roll->addInput('name', 'sqllinks');
		$form->roll->input->name->setTitle('Name');
		$form->roll->input->name->setLinks('admin/user/group_field_form');
		$form->roll->input->name->setModal();
		$form->roll->input->name->setModalReload();
		$form->roll->input->name->setModalLarge();
		$form->roll->input->name->setDisplayColumn();

		$form->roll->addInput('title', 'sqlplaintext');
		$form->roll->input->title->setTitle('Title');
		$form->roll->input->title->setDisplayColumn();

		$form->roll->addInput('form', 'sqlplaintext');
		$form->roll->input->form->setTitle('Form Type');
		$form->roll->input->form->setDisplayColumn();

		$form->roll->addInput('required', 'checkbox');
		$form->roll->input->required->setTitle('Required');
		$form->roll->input->required->setCaption('yes');
		$form->roll->input->required->setPlaintext();
		$form->roll->input->required->setDisplayColumn();

		$form->roll->addInput('params', 'sqlplaintext');
		$form->roll->input->params->setTitle('Parameter');
		$form->roll->input->params->setDisplayColumn();
		$form->roll->input->params->setDisplayFunction(function($value='')
		{
			$value = @(array)json_decode($value, 1);
			$out   = array();
			$out2  = array();
			foreach ($value as $key1 => $value1) {
				$out[$value1['method']] = @intval($out[$value1['method']]) + 1;
			}
			foreach ($out as $key1 => $value1) {
				if ($value1 != 1) {
					$key1 .= '('.$value1.')';
				}
				$out2[] = $key1;
			}
			return implode(', ', $out2);
		});

		$form->roll->addInput('orderby', 'orderby');
		$form->roll->input->orderby->setTitle('Ordered');
		$form->roll->input->orderby->setDisplayColumn();
		if (isset($_GET[$form->roll->sortConfig['get_name']])) {
			$form->roll->input->orderby->setPlaintext();
		}

		$form->roll->addInput('created', 'datetime');
		$form->roll->input->created->setTitle('Created');
		$form->roll->input->created->setPlainText();
		$form->roll->input->created->setDisplayColumn();

		$form->roll->addInput('updated', 'datetime');
		$form->roll->input->updated->setTitle('Updated');
		$form->roll->input->updated->setPlainText();
		$form->roll->input->updated->setDisplayColumn(false);
		
		$form->roll->addReportAll();
		$form->roll->action();
		echo $form->roll->getForm();
		$this->_tpl_model->show();
	}
	function group_field_form()
	{
		$id       = @intval($_GET['id']);
		$group_id = @intval($_GET['group_id']);
		$form     = $this->_pea_model->newForm('user_field');

		$_GET['return'] = '';
		$this->_tpl_model->setLayout('blank');
		
		$form->initEdit(!empty($id) ? 'WHERE `id`='.$id : '');

		if (!$id) {
			$group_title = $this->_tpl_model->_db_model->getOne('SELECT `title` FROM `user_group` WHERE `id`='.$group_id);
			if ($group_title) {
				$form->edit->addExtraField('group_id', $group_id);
			}
		}

		$form->edit->setHeader(!empty($id) ? 'Edit Field' : 'Add Field');
		$form->edit->setModalResponsive();
		$form->edit->bodyWrap($form->edit->formBodyBefore.'<div class="row">','</div>'.$form->edit->formBodyAfter);

		$form->edit->addInput('name', 'text');
		$form->edit->input->name->setTitle('Name');
		$form->edit->input->name->setRequire();
		$form->edit->input->name->formWrap('<div class="col-xs-12 col-sm-6">','</div>');
		$name = $form->edit->input->name->getName();
		if (!empty($_POST[$name])) {
			$_POST[$name] = preg_replace('~[^a-z0-9]~', '_', strtolower($_POST[$name]));
		}

		$form->edit->addInput('title', 'text');
		$form->edit->input->title->setTitle('Title');
		$form->edit->input->title->setRequire();
		$form->edit->input->title->formWrap('<div class="col-xs-12 col-sm-6">','</div>');

		$form_type = ['text','checkbox','date','datetime','file','select','multiselect','textarea'];
		$form->edit->addInput('form', 'select');
		$form->edit->input->form->setTitle('Form Type');
		$form->edit->input->form->setRequire();
		$form->edit->input->form->addAttr('id="s_form"');
		$form->edit->input->form->formWrap('<div class="col-xs-12 col-sm-6">','</div>');
		foreach ($form_type as $value) {
			$form->edit->input->form->addOption($value, $value);
		}

		$form->edit->addInput('required', 'checkbox');
		$form->edit->input->required->setTitle('Required');
		$form->edit->input->required->setCaption('yes');
		$form->edit->input->required->formWrap('<div class="col-xs-12 col-sm-6">','</div>');

		$form->edit->addInput('params', 'textarea');
		$form->edit->input->params->setTitle('Parameter');
		$form->edit->input->params->addAttr('id="s_params"');		
		$form->edit->input->params->addTip('<div id="s_params_result">
			<div class="form-inline" style="margin-bottom: 10px;">
				<div class="form-group">
					<select name="params[{index}][method]" class="form-control s_method">
						<option value="">-- Select Method --</option>
					</select>
				</div>
				<div class="form-group s_args" data-index="{index}">
					<input type="text" name="params[{index}][args][]" class="form-control i_text" title="{data}" placeholder="{data}" value="{value}" size="40">
					<input type="number" name="params[{index}][args][]" class="form-control i_number" title="{data}" placeholder="{data}" value="{value}">
					<select name="params[{index}][args][]" class="form-control i_select">
						{data}
					</select>
				</div>
				<div class="form-group">
					<a href="#" class="btn btn-danger"><i class="fa fa-trash"></i></a>
				</div>
			</div>
		</div>
		<a id="s_params_add" href="#" class="btn btn-default"><i class="fa fa-plus"></i></a>');		
		$form->edit->input->params->formWrap('<div class="col-xs-12 col-sm-12">','</div>');		
		$params = $form->edit->input->params->getName();
		if (isset($_POST[$params])) {
			$params_allow   = array('setCaption','setType','addTip','addClass','addAttr','setFormat','setDefaultValue','setDateFormat','setDateFormatInput','setMinDate','setMaxDate','setFolder','setAllowedExtension','setResize','setThumbnail','setImageClick','setDocumentViewer','addOption','setHtmlEditor');
			$_POST[$params] = (isset($_POST['params'])) ? (array)$_POST['params'] : array();
			foreach ($_POST[$params] as $key => $value) {
				if (empty($value['method'])) {
					unset($_POST[$params][$key]);
				}elseif (!in_array($value['method'], $params_allow)) {
					unset($_POST[$params][$key]);
				}elseif (empty($value['args'])) {
					$_POST[$params][$key]['args'] = array();
				}
			}
			$_POST[$params] = json_encode(array_values($_POST[$params]));
		}

		$this->_tpl_model->js('controllers/admin/user_group_field_form.js');

		$form->edit->action();
		echo $form->edit->getForm();
		$this->_tpl_model->show();
	}

	function login()
	{
		if ($this->_tpl_model->_url_current != $this->_tpl_model->_url.$this->_tpl_model->config('dashboard', 'login_uri') and !isset($_GET['acc'])) {
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
				if (count($token) == 2) {
					if (!empty($_POST[$token[0]]) and !empty($_POST[$token[1]])) {
						if ($this->_tpl_model->user_login($_POST[$token[0]], $_POST[$token[1]], 1, isset($_POST['remember']))) {
							redirect($this->_tpl_model->_url.'admin/dashboard');
						}else{
							$input['msg'] = $this->_tpl_model->msg($this->_tpl_model->user_msg(), 'danger');
						}
					}
				}
			}else{
				$input['msg'] = $this->_tpl_model->msg('Token Expired', 'danger');
			}
		}
		
		if (isset($_GET['acc'])) {
			if ($this->_tpl_model->user_login_with($_GET['acc'], 1)) {
				redirect($this->_tpl_model->_url.'admin/dashboard');
			}else{
				$input['msg'] = $this->_tpl_model->msg($this->_tpl_model->user_msg(), 'danger');
			}
		}

		if ($this->_tpl_model->user_login_remember(1)) {
			redirect($this->_tpl_model->_url.'admin/dashboard');
		}

		$input['token'] = $this->_encrypt_model->encodeToken($input['usr'].'|'.$input['pwd'], 2);

		$this->_tpl_model->setLayout('blank');
		$this->_tpl_model->view('User/login', ['input' => $input]);
		$this->_tpl_model->show();
	}
	function login_auto()
	{
		$id = @intval($_GET['id']);
		if ($id) {
			$user = $this->_tpl_model->_db_model->getRow('SELECT `username`,`password` FROM `user` WHERE `id`='.$id);
			if ($user) {
				$this->load->model('_encrypt_model');
				$user['password'] = $this->_encrypt_model->decode($user['password']);
				if ($this->_tpl_model->user_login($user['username'], $user['password'])) {
					redirect($this->_tpl_model->_url.$this->_tpl_model->config('user','home_uri'));
				}else{
					echo $this->_tpl_model->msg($this->_tpl_model->user_msg(), 'danger');
				}
			}
		}
		$this->_tpl_model->show();
	}
	function logout()
	{
		$this->_tpl_model->user_logout(1);
		redirect($this->_tpl_model->_url.$this->_tpl_model->config('dashboard', 'login_uri'));
	}

	function notif()
	{
		$this->_tpl_model->setLayout('blank');
		echo '<div style="padding: 15px;">';

		$form = $this->_pea_model->newForm('user_notif');

		$form->initSearch();

		$form->search->addInput('created', 'dateinterval');
		$form->search->input->created->setTitle('Date & Time');
		
		$form->search->addInput('keyword','keyword');
		$form->search->input->keyword->setTitle('Search');
		$form->search->input->keyword->addSearchField('title,info');

		$form->search->addInput('status', 'select');
		$form->search->input->status->setTitle('Status');
		$form->search->input->status->addOption('-- Status --');
		$form->search->input->status->addOption('Unread', '0');
		$form->search->input->status->addOption('Read', '1');
		
		$add_sql = $form->search->action();
		$keyword = $form->search->keyword();
		
		echo $form->search->getForm();
	
		$form->initRoll($add_sql.' AND (`user_id`='.$this->_tpl_model->user['id'].' OR `group_id` IN('.implode($this->_tpl_model->user['group_ids']).') OR (`user_id`=0 AND `group_id`=0)) AND `type`=1 ORDER BY `id` DESC');

		$form->roll->addInput('created', 'datetime');
		$form->roll->input->created->setTitle('Date & Time');
		$form->roll->input->created->setPlainText();

		$form->roll->addInput('title', 'sqlplaintext');
		$form->roll->input->title->setTitle('Title');
		$form->roll->input->title->setDisplayFunction(function($value='', $id=0)
		{
			return '<a href="'.$this->_tpl_model->_url.'_T/notif_detail?id='.$id.'&type=1">'.$value.'</a>';
		});

		$form->roll->addInput('info', 'sqlplaintext');
		$form->roll->input->info->setTitle('Info');

		$form->roll->addInput('status', 'select');
		$form->roll->input->status->setTitle('Status');
		$form->roll->input->status->addOption('Unread', '0');
		$form->roll->input->status->addOption('Read', '1');
		$form->roll->input->status->setPlainText();

		$form->roll->setSaveTool(false);
		$form->roll->setDeleteTool(false);
		$form->roll->addReportAll();
		$form->roll->action();
		echo $form->roll->getForm();

		echo '</div>';
		$this->_tpl_model->show();
	}

	function address()
	{
		$_GET['return'] = '';
		$this->_tpl_model->setLayout('blank');

		$form = $this->_pea_model->newForm('user_address');

		$form->initSearch();

		$form->search->addInput('keyword', 'keyword');
		$form->search->input->keyword->setTitle('Search');
		$form->search->input->keyword->addSearchField('title,email,phone,province_title,city_title,district_title,village_title,address,zip_code');
		
		$form->search->addExtraField('user_id', $this->_tpl_model->user['id']);

		$add_sql = $form->search->action();
		$keyword = $form->search->keyword();
		
		$form->initRoll($add_sql.' ORDER BY `id` DESC');

		$form->roll->setHeader('Manage Address List');
		$form->roll->bodyWrap($form->roll->formBodyBefore.$form->search->getForm().$this->_tpl_model->button('admin/user/address_form', 'Add Address', 'fa fa-plus', 'modal_reload', 'style="margin-right: 10px; margin-bottom: 15px;"', 1), $form->roll->formBodyAfter);

		$form->roll->addInput('title', 'sqllinks');
		$form->roll->input->title->setTitle('Title');
		$form->roll->input->title->setLinks('admin/user/address_form');
		$form->roll->input->title->setModal();
		$form->roll->input->title->setModalReload();
		$form->roll->input->title->setDisplayColumn();

		$form->roll->addInput('email', 'sqlplaintext');
		$form->roll->input->email->setTitle('Email');
		$form->roll->input->email->setDisplayColumn();

		$form->roll->addInput('phone', 'sqlplaintext');
		$form->roll->input->phone->setTitle('Phone');
		$form->roll->input->phone->setDisplayColumn();

		$form->roll->addInput('location_detail', 'sqlplaintext');
		$form->roll->input->location_detail->setTitle('Address');
		$form->roll->input->location_detail->setFieldName('CONCAT(`address`," ",`village_title`,", ",`district_title`,", ",`city_title`,", ",`province_title`,", ",`zip_code`)');
		$form->roll->input->location_detail->setDisplayColumn();

		$form->roll->addInput('main', 'checkbox');
		$form->roll->input->main->setTitle('Main Address');
		$form->roll->input->main->setCaption('yes');
		$form->roll->input->main->setDisplayColumn();
		$form->roll->input->main->setPlainText();

		$form->roll->addInput('created', 'datetime');
		$form->roll->input->created->setTitle('Created');
		$form->roll->input->created->setPlainText();
		$form->roll->input->created->setDisplayColumn();

		$form->roll->addInput('updated', 'datetime');
		$form->roll->input->updated->setTitle('Updated');
		$form->roll->input->updated->setPlainText();
		$form->roll->input->updated->setDisplayColumn(false);
		
		$form->roll->setDeleteCondition('{main} == 1');

		$form->roll->setSaveTool(false);
		$form->roll->addReportAll();
		$form->roll->action();
		echo $form->roll->getForm();
		$this->_tpl_model->show();
	}
	function address_form()
	{
		$id   = @intval($_GET['id']);
		$form = $this->_pea_model->newForm('user_address');

		$this->_tpl_model->js('controllers/admin/user_address.js');

		$_GET['return'] = '';
		$this->_tpl_model->setLayout('blank');

		$form->initEdit(!empty($id) ? 'WHERE `id`='.$id : '');
		
		$form->edit->setHeader(!empty($id) ? 'Edit Address' : 'Add Address');
		$form->edit->setModalResponsive();
		
		$form->edit->addInput('title','text');
		$form->edit->input->title->setTitle('Title');
		$form->edit->input->title->setRequire();

		$form->edit->addInput('email','text');
		$form->edit->input->email->setTitle('Email');
		$form->edit->input->email->setType('email');
		$form->edit->input->email->setRequire();
		$form->edit->input->email->addAttr('id="i_email"');
		
		$form->edit->addInput('phone','text');
		$form->edit->input->phone->setTitle('Phone');
		$form->edit->input->phone->setType('tel');
		$form->edit->input->phone->setRequire();
		$form->edit->input->phone->addAttr('id="i_phone"');

		$form->edit->addInput('location_input', 'multiinput');
		$form->edit->input->location_input->setTitle('Location');

		$form->edit->input->location_input->addInput('province_id', 'selecttable');
		$form->edit->input->location_input->element->province_id->setTitle('Province');
		$form->edit->input->location_input->element->province_id->setReferenceTable('location');
		$form->edit->input->location_input->element->province_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_input->element->province_id->setReferenceCondition( '`type_id`=1' );
		$form->edit->input->location_input->element->province_id->setReferenceOrderBy( 'title' );
		$form->edit->input->location_input->element->province_id->addOption( '-- Select Province --', '' );
		$form->edit->input->location_input->element->province_id->setRequire();

		$form->edit->input->location_input->addInput('city_id', 'selecttable');
		$form->edit->input->location_input->element->city_id->setTitle('City');
		$form->edit->input->location_input->element->city_id->setReferenceTable('location');
		$form->edit->input->location_input->element->city_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_input->element->city_id->setReferenceCondition( '`type_id`=2' );
		$form->edit->input->location_input->element->city_id->setReferenceOrderBy( 'title' );
		$form->edit->input->location_input->element->city_id->setDependent( $form->edit->input->location_input->element->province_id->getName(), 'par_id' );
		$form->edit->input->location_input->element->city_id->addOption( '-- Select City --', '' );
		$form->edit->input->location_input->element->city_id->setRequire();

		$form->edit->input->location_input->addInput('district_id', 'selecttable');
		$form->edit->input->location_input->element->district_id->setTitle('District');
		$form->edit->input->location_input->element->district_id->setReferenceTable('location');
		$form->edit->input->location_input->element->district_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_input->element->district_id->setReferenceCondition( '`type_id`=3' );
		$form->edit->input->location_input->element->district_id->setReferenceOrderBy( 'title' );
		$form->edit->input->location_input->element->district_id->setDependent( $form->edit->input->location_input->element->city_id->getName(), 'par_id' );
		$form->edit->input->location_input->element->district_id->addOption( '-- Select District --', '' );
		$form->edit->input->location_input->element->district_id->setRequire();

		$form->edit->input->location_input->addInput('village_id', 'selecttable');
		$form->edit->input->location_input->element->village_id->setTitle('Village');
		$form->edit->input->location_input->element->village_id->setReferenceTable('location');
		$form->edit->input->location_input->element->village_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_input->element->village_id->setReferenceCondition( '`type_id`=4' );
		$form->edit->input->location_input->element->village_id->setReferenceOrderBy( 'title' );
		$form->edit->input->location_input->element->village_id->setDependent( $form->edit->input->location_input->element->district_id->getName(), 'par_id' );
		$form->edit->input->location_input->element->village_id->addOption( '-- Select Village --', '' );
		$form->edit->input->location_input->element->village_id->setRequire();
			
		$form->edit->addInput('zip_code','text');
		$form->edit->input->zip_code->setTitle('Zip Code');
		$form->edit->input->zip_code->setType('number');
		$form->edit->input->zip_code->setRequire();

		$form->edit->addInput('address', 'text');
		$form->edit->input->address->setTitle('Address');
		$form->edit->input->address->setRequire();
		$form->edit->input->address->addTip('Jln. Jendral Sudirman No.123 RT.05 RW.06');

		$form->edit->addInput('main', 'checkbox');
		$form->edit->input->main->setTitle('Main Address');
		$form->edit->input->main->setCaption('yes');
		$form->edit->input->main->setDefaultValue(0);
		$form->edit->input->main->addAttr('id="i_main"');

		$form->edit->addExtraField('user_id', $this->_tpl_model->user['id']);
		
		$form->edit->onSave(function($id, $f)
		{
			$data = $f->db->getRow('SELECT * FROM `user_address` WHERE `id`='.$id);
			if ($data) {
				$data_update = array(
					'province_title' => $f->db->getOne('SELECT `title` FROM `location` WHERE `id`='.$data['province_id']),
					'city_title'     => $f->db->getOne('SELECT `title` FROM `location` WHERE `id`='.$data['city_id']),
					'district_title' => $f->db->getOne('SELECT `title` FROM `location` WHERE `id`='.$data['district_id']),
					'village_title'  => $f->db->getOne('SELECT `title` FROM `location` WHERE `id`='.$data['village_id']),
				);
				if ($data['main']) {
					$user_update                = $data_update;
					$user_update['province_id'] = $data['province_id'];
					$user_update['city_id']     = $data['city_id'];
					$user_update['district_id'] = $data['district_id'];
					$user_update['village_id']  = $data['village_id'];
					$user_update['zip_code']    = $data['zip_code'];
					$user_update['address']     = $data['address'];
					
					$f->db->update('user', $user_update, $data['user_id']);

					$data_user = $f->db->getRow('SELECT `email`,`phone` FROM `user` WHERE `id`='.$data['user_id']);

					$data_update['email'] = $data_user['email'];
					$data_update['phone'] = $data_user['phone'];

					$f->db->update('user_address', ['main' => 0], '`user_id`='.$data['user_id'].' AND `id`!='.$id);
				}
				$f->db->update('user_address', $data_update, $id);
			}
		});
		$form->edit->action();
		echo $form->edit->getForm();
		$this->_tpl_model->show();
	}

	function log()
	{
		$id   = @intval($_GET['id']);
		$form = $this->_pea_model->newForm('user_log');

		if ($id) {
			$user = $this->_tpl_model->_db_model->getRow('SELECT `name`,`username` FROM `user` WHERE `id`='.$id);
			if ($user) {
				$this->_tpl_model->nav_add('admin/user/log?id='.$id, 'Login History');
				$this->_tpl_model->nav_add($user['name'].' ['.$user['username'].']');
				if ($id == $this->_tpl_model->user['id']) {
					$logout_set = @intval($_POST['logout_set']);
					if ($logout_set) {
						$this->_tpl_model->_db_model->update('user_log', array(
							'logout' => date('Y-m-d H:i:s'),
						), '`id`='.$logout_set.' AND `user_id`='.$id);
						echo $this->_tpl_model->msg('Session Ended', 'success');
					}
				}
			}
		}

		$form->initSearch();

		$form->search->addInput('type', 'select');
		$form->search->input->type->setTitle('Type');
		$form->search->input->type->addOption('-- Select Type --', '');
		$form->search->input->type->addOptions($this->_tpl_model->user_group_type);
		
		$form->search->addInput('keyword','keyword');
		$form->search->input->keyword->setTitle('Device');
		$form->search->input->keyword->addSearchField('device');
		
		$form->search->addInput('keyword2','keyword');
		$form->search->input->keyword2->setTitle('IP Address');
		$form->search->input->keyword2->addSearchField('ip');

		$form->search->addInput('login', 'dateinterval');
		$form->search->input->login->setTitle('Login Date');

		$form->search->addInput('logout', 'dateinterval');
		$form->search->input->logout->setTitle('Logout Date');
		
		$form->search->addExtraField('user_id', $id);

		$add_sql = $form->search->action();
		$keyword = $form->search->keyword();

		echo $form->search->getForm();

		$form->initRoll($add_sql.' ORDER BY `id` DESC');

		$form->roll->addInput('type', 'select');
		$form->roll->input->type->setTitle('Type');
		$form->roll->input->type->addOptions($this->_tpl_model->user_group_type);
		$form->roll->input->type->setPlainText();
		$form->roll->input->type->setDisplayColumn();
		
		$form->roll->addInput('device','sqlplaintext');
		$form->roll->input->device->setTitle('Device');
		$form->roll->input->device->setDisplayColumn();

		$form->roll->addInput('ip','sqlplaintext');
		$form->roll->input->ip->setTitle('IP Address');
		$form->roll->input->ip->setDisplayColumn();

		$form->roll->addInput('login', 'datetime');
		$form->roll->input->login->setTitle('Login Date');
		$form->roll->input->login->setPlainText();
		$form->roll->input->login->setDisplayColumn();

		$form->roll->addInput('logout', 'datetime');
		$form->roll->input->logout->setTitle('Logout Date');
		$form->roll->input->logout->setPlainText();
		$form->roll->input->logout->setDisplayColumn();
		if ($id == $this->_tpl_model->user['id']) {
			$form->roll->input->logout->setDisplayFunction(function($value='', $id=0, $index='', $values=array())
			{
				if ($values[$index]['logout'] == '0000-00-00 00:00:00') {
					if ($this->_tpl_model->user['log_id'] == $values[$index]['roll_id']) {
						$value = 'This Device';
					}else{
						$value = '<button class="btn btn-danger btn-xs" type="submit" name="logout_set" value="'.$values[$index]['roll_id'].'" onclick="return confirm(\'End Session ?\')">End Session</button>';
					}
				}
				return $value;
			});
		}
		
		$form->roll->setSaveTool(false);
		$form->roll->setDeleteTool(false);
		$form->roll->addReportAll();
		$form->roll->action();
		echo $form->roll->getForm();
		$this->_tpl_model->show();
	}
}

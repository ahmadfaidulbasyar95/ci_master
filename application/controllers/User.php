<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller 
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('_pea_model');
		$this->load->model('_tpl_model');

		if (!in_array($this->_tpl_model->method, ['login','logout','register'])) {
			$this->_tpl_model->user_login_validate();
		}
	}

	function profile()
	{
		$this->register();
	}

	function register()
	{
		$id   = @intval($this->_tpl_model->user['id']);
		$form = $this->_pea_model->newForm('user');

		$form->initEdit(!empty($id) ? 'WHERE `id`='.$id : '');

		if (!$id) {
			$group_id = @intval($_GET['id']);
			if (!$group_id) {
				$group_id = intval($this->_tpl_model->config('user', 'group_id'));
			}
			$group_title = $this->_tpl_model->_db_model->getOne('SELECT `title` FROM `user_group` WHERE `id`='.$group_id);
			$form->edit->setData('group_id', $group_id);
		}
		
		$form->edit->setHeader(!empty($id) ? 'Profil' : 'Pendaftaran '.$group_title);
		
		$form->edit->addInput('name','text');
		$form->edit->input->name->setTitle('Nama');
		$form->edit->input->name->setRequire();

		if (!$id) {					
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
					$form->edit->input->password->msg = str_replace('{msg}', '<b>Password dan Re-Password</b> tidak sama', $form->edit->input->password->failMsgTpl);
				}else{
					$this->load->model('_encrypt_model');
					$_POST['pwd'] = $_POST[$name];
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
			$form->edit->input->phone->setTitle('No HP');
			$form->edit->input->phone->setType('number');
			$form->edit->input->phone->setRequire();
			$form->edit->input->phone->setUniq();
		}else{
			$form->edit->addInput('username', 'sqlplaintext');
			$form->edit->input->username->setTitle('Username');
			$form->edit->input->username->addTip($this->_tpl_model->button('user/usr?return='.urlencode($this->_tpl_model->_url_current), 'Ubah', 'fa fa-repeat', 'btn-sm modal_reload', '', 1));

			$form->edit->addInput('password', 'plaintext');
			$form->edit->input->password->setTitle('Password');
			$form->edit->input->password->setValue($this->_tpl_model->button('user/pwd?return='.urlencode($this->_tpl_model->_url_current), 'Ubah', 'fa fa-repeat', 'btn-sm modal_reload', '', 1));

			$form->edit->addInput('email', 'sqlplaintext');
			$form->edit->input->email->setTitle('Email');
			$form->edit->input->email->addTip($this->_tpl_model->button('user/usr?act=email&return='.urlencode($this->_tpl_model->_url_current), 'Ubah', 'fa fa-repeat', 'btn-sm modal_reload', '', 1));

			$form->edit->addInput('phone', 'sqlplaintext');
			$form->edit->input->phone->setTitle('No HP');
			$form->edit->input->phone->addTip($this->_tpl_model->button('user/usr?act=phone&return='.urlencode($this->_tpl_model->_url_current), 'Ubah', 'fa fa-repeat', 'btn-sm modal_reload', '', 1));
		}

		$form->edit->addInput('image', 'file');
		$form->edit->input->image->setTitle('Foto');
		$form->edit->input->image->setFolder('files/user/');
		$form->edit->input->image->setAllowedExtension(array('jpg', 'jpeg', 'gif', 'png', 'bmp'));
		$form->edit->input->image->setResize(1080);
		$form->edit->input->image->setImageClick();
		$form->edit->input->image->setThumbnail(120, 'thumb/');

		$form->edit->addInput('gender', 'select');
		$form->edit->input->gender->setTitle('Jenis Kelamin');
		$form->edit->input->gender->addOption('-- Pilih Jenis Kelamin --', '');
		$form->edit->input->gender->addOption('Laki-laki', 1);
		$form->edit->input->gender->addOption('Perempuan', 2);
		$form->edit->input->gender->setRequire();

		$form->edit->addInput('birth_date', 'date');
		$form->edit->input->birth_date->setTitle('Tanggal Lahir');
		$form->edit->input->birth_date->setRequire();
		$form->edit->input->birth_date->setMaxDate('-7 YEARS');

		$form->edit->addInput('location_input', 'multiinput');
		$form->edit->input->location_input->setTitle('Lokasi');

		$form->edit->input->location_input->addInput('province_id', 'selecttable');
		$form->edit->input->location_input->element->province_id->setTitle('Provinsi');
		$form->edit->input->location_input->element->province_id->setReferenceTable('location');
		$form->edit->input->location_input->element->province_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_input->element->province_id->setReferenceCondition( '`type_id`=1' );
		$form->edit->input->location_input->element->province_id->addOption( '-- Pilih Provinsi --', '' );
		$form->edit->input->location_input->element->province_id->setRequire();

		$form->edit->input->location_input->addInput('city_id', 'selecttable');
		$form->edit->input->location_input->element->city_id->setTitle('Kabupaten');
		$form->edit->input->location_input->element->city_id->setReferenceTable('location');
		$form->edit->input->location_input->element->city_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_input->element->city_id->setReferenceCondition( '`type_id`=2' );
		$form->edit->input->location_input->element->city_id->setDependent( $form->edit->input->location_input->element->province_id->getName(), 'par_id' );
		$form->edit->input->location_input->element->city_id->addOption( '-- Pilih Kabupaten --', '' );
		$form->edit->input->location_input->element->city_id->setRequire();

		$form->edit->input->location_input->addInput('district_id', 'selecttable');
		$form->edit->input->location_input->element->district_id->setTitle('Kecamatan');
		$form->edit->input->location_input->element->district_id->setReferenceTable('location');
		$form->edit->input->location_input->element->district_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_input->element->district_id->setReferenceCondition( '`type_id`=3' );
		$form->edit->input->location_input->element->district_id->setDependent( $form->edit->input->location_input->element->city_id->getName(), 'par_id' );
		$form->edit->input->location_input->element->district_id->addOption( '-- Pilih Kecamatan --', '' );
		$form->edit->input->location_input->element->district_id->setRequire();

		$form->edit->input->location_input->addInput('village_id', 'selecttable');
		$form->edit->input->location_input->element->village_id->setTitle('Desa');
		$form->edit->input->location_input->element->village_id->setReferenceTable('location');
		$form->edit->input->location_input->element->village_id->setReferenceField( 'title', 'id' );
		$form->edit->input->location_input->element->village_id->setReferenceCondition( '`type_id`=4' );
		$form->edit->input->location_input->element->village_id->setDependent( $form->edit->input->location_input->element->district_id->getName(), 'par_id' );
		$form->edit->input->location_input->element->village_id->addOption( '-- Pilih Desa --', '' );
		$form->edit->input->location_input->element->village_id->setRequire();

		$form->edit->addInput('address', 'text');
		$form->edit->input->address->setTitle('Alamat');
		$form->edit->input->address->setRequire();
		$form->edit->input->address->addTip('Jln. Jendral Sudirman No.123 RT.05 RW.06');
		
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
				$group_id = $f->getData('group_id');
				if (empty($data['group_ids']) and $group_id) {
					$data_update['group_ids'] = json_encode(array((string)$group_id));
				}
				$f->db->update('user', $data_update, $id);
				if (isset($_POST['pwd'])) {
					if ($this->_tpl_model->user_login($data['username'], $_POST['pwd'])) {
						if (empty($_SESSION['user_return'])) {
							$url = $this->_tpl_model->_url.$this->_tpl_model->config('user','home_uri');
						}else{
							$url = $_SESSION['user_return'];
							unset($_SESSION['user_return']);
							if (isset($_SESSION['user_post'])) {
								$_SESSION['user_post_load'] = $_SESSION['user_post'];
								unset($_SESSION['user_post']);
							}
						}
						redirect($url);
					}
				}
			}
		});
		if (!$id) {
			$form->edit->setSaveButton('<i class="far fa-paper-plane"></i> Submit');
		}
		$form->edit->action();
		echo $form->edit->getForm();
		$this->_tpl_model->show();
	}

	function pwd()
	{
		$id             = @intval($this->_tpl_model->user['id']);
		$_GET['return'] = '';
		$this->_tpl_model->setLayout('blank');
		if ($id) {
			$form = $this->_pea_model->newForm('user');
			
			$form->initEdit('WHERE `id`='.$id);
			
			$form->edit->setHeader('Ubah Password');
			$form->edit->setModalResponsive();

			$form->edit->addInput('password','text');
			$form->edit->input->password->setTitle('Password Baru');
			$form->edit->input->password->setType('password');
			$form->edit->input->password->setRequire();
			$name = $form->edit->input->password->getName();
			if (!empty($_POST[$name])) {
				if ($_POST[$name] != @$_POST['password_re']) {
					$form->edit->input->password->msg = str_replace('{msg}', '<b>Password Baru dan Re-Password</b> tidak sama', $form->edit->input->password->failMsgTpl);
				}else{
					$this->load->model('_encrypt_model');
					$_POST[$name] = $this->_encrypt_model->encode($_POST[$name]);
				}
			}

			$form->edit->addInput('password_re', 'plaintext');
			$form->edit->input->password_re->setTitle('Re-Password');
			$form->edit->input->password_re->setValue('<input type="password" name="password_re" class="form-control" value="" title="Re-Password" placeholder="Re-Password" required="required">');

			$form->edit->addInput('password_current', 'plaintext');
			$form->edit->input->password_current->setTitle('Password Lama');
			$form->edit->input->password_current->setValue('<input type="password" name="password_current" class="form-control" value="" title="Password Lama" placeholder="Password Lama" required="required">');
			if (!empty($_POST[$name]) and !empty($_POST['password_current'])) {
				if (!$this->_tpl_model->user_pwd_validate($_POST['password_current'])) {
					$form->edit->input->password->msg = str_replace('{msg}', '<b>Password Lama tidak valid', $form->edit->input->password->failMsgTpl);
				}
			}

			$form->edit->onSave(function($id, $f)
			{
				$pwd = $f->db->getOne('SELECT `password` FROM `user` WHERE `id`='.$id);
				if ($pwd) {
					$_SESSION['user_login'][0]['password'] = $pwd;
				}
			});
			$form->edit->action();
			$form->edit->input->password->setValue('');
			echo $form->edit->getForm();
			$this->_tpl_model->show();
		}
	}

	function usr()
	{
		$id             = @intval($this->_tpl_model->user['id']);
		$_GET['return'] = '';
		$this->_tpl_model->setLayout('blank');
		if ($id) {
			$form = $this->_pea_model->newForm('user');
			
			$form->initEdit('WHERE `id`='.$id);
			
			$form->edit->setModalResponsive();

			switch (@$_GET['act']) {
				case 'email':
					$form->edit->setHeader('Ubah Email');
					
					$form->edit->addInput('email','text');
					$form->edit->input->email->setTitle('Email');
					$form->edit->input->email->setType('email');
					$form->edit->input->email->setRequire();
					$form->edit->input->email->setUniq();

					$input = 'email';
					break;

				case 'phone':
					$form->edit->setHeader('Ubah No HP');
					
					$form->edit->addInput('phone','text');
					$form->edit->input->phone->setTitle('No HP');
					$form->edit->input->phone->setType('number');
					$form->edit->input->phone->setRequire();
					$form->edit->input->phone->setUniq();

					$input = 'phone';
					break;		
				
				default:
					$form->edit->setHeader('Ubah Username');
					
					$form->edit->addInput('username','text');
					$form->edit->input->username->setTitle('Username');
					$form->edit->input->username->setRequire();
					$form->edit->input->username->setUniq();

					$input = 'username';
					break;
			}

			$form->edit->addInput('password_current', 'plaintext');
			$form->edit->input->password_current->setTitle('Password');
			$form->edit->input->password_current->setValue('<input type="password" name="password_current" class="form-control" value="" title="Password" placeholder="Password" required="required">');
			$name = $form->edit->input->$input->getName();
			if (!empty($_POST[$name]) and !empty($_POST['password_current'])) {
				if (!$this->_tpl_model->user_pwd_validate($_POST['password_current'])) {
					$form->edit->input->$input->msg = str_replace('{msg}', '<b>Password tidak valid', $form->edit->input->$input->failMsgTpl);
				}
			}

			$form->edit->action();
			echo $form->edit->getForm();
			$this->_tpl_model->show();
		}
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
				$token = explode('|', $token);
				if (count($token) == 2) {
					if (!empty($_POST[$token[0]]) and !empty($_POST[$token[1]])) {
						if ($this->_tpl_model->user_login($_POST[$token[0]], $_POST[$token[1]])) {
							if (empty($_SESSION['user_return'])) {
								$url = $this->_tpl_model->_url.$this->_tpl_model->config('user','home_uri');
							}else{
								$url = $_SESSION['user_return'];
								unset($_SESSION['user_return']);
								if (isset($_SESSION['user_post'])) {
									$_SESSION['user_post_load'] = $_SESSION['user_post'];
									unset($_SESSION['user_post']);
								}
							}
							redirect($url);
						}else{
							$input['msg'] = $this->_tpl_model->msg($this->_tpl_model->user_msg(), 'danger');
						}
					}
				}
			}else{
				$input['msg'] = $this->_tpl_model->msg('Token Expired', 'danger');
			}
		}
		$input['token'] = $this->_encrypt_model->encodeToken($input['usr'].'|'.$input['pwd'], 2);

		$this->_tpl_model->view('User/login', ['input' => $input]);
		$this->_tpl_model->show();
	}
	function logout()
	{
		$this->_tpl_model->user_logout();
		redirect($this->_tpl_model->_url.'user/login');
	}
}

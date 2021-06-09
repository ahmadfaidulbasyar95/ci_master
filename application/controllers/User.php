<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller 
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('_pea_model');
		$this->load->model('_tpl_model');

		if (!in_array($this->_tpl_model->method, ['login','logout','register','detail'])) {
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
			$form->edit->input->phone->setType('tel');
			$form->edit->input->phone->setRequire();
			$form->edit->input->phone->setUniq();
		}else{
			$form->edit->addInput('username', 'sqlplaintext');
			$form->edit->input->username->setTitle('Username');
			$form->edit->input->username->addTip($this->_tpl_model->button('user/usr', 'Ubah', 'fa fa-repeat', 'btn-sm modal_reload', '', 1));

			$form->edit->addInput('password', 'plaintext');
			$form->edit->input->password->setTitle('Password');
			$form->edit->input->password->setValue($this->_tpl_model->button('user/pwd', 'Ubah', 'fa fa-repeat', 'btn-sm modal_reload', '', 1));

			$form->edit->addInput('email', 'sqlplaintext');
			$form->edit->input->email->setTitle('Email');
			$form->edit->input->email->addTip($this->_tpl_model->button('user/usr?act=email', 'Ubah', 'fa fa-repeat', 'btn-sm modal_reload', '', 1));

			$form->edit->addInput('phone', 'sqlplaintext');
			$form->edit->input->phone->setTitle('No HP');
			$form->edit->input->phone->addTip($this->_tpl_model->button('user/usr?act=phone', 'Ubah', 'fa fa-repeat', 'btn-sm modal_reload', '', 1));

			$telegram_conf = $this->_tpl_model->config('notif_telegram');
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
				$form->edit->input->telegram_data->addTip($this->_tpl_model->button('user/usr?act=telegram', 'Sambungkan', 'fa fa-link', 'btn-sm modal_reload', '', 1));
			}
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
			
		$form->edit->addInput('zip_code','text');
		$form->edit->input->zip_code->setTitle('Kode Pos');
		$form->edit->input->zip_code->setType('number');
		$form->edit->input->zip_code->setRequire();

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
		$id = @intval($this->_tpl_model->user['id']);
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
		$id = @intval($this->_tpl_model->user['id']);
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
					$form->edit->input->phone->setType('tel');
					$form->edit->input->phone->setRequire();
					$form->edit->input->phone->setUniq();

					$input = 'phone';
					break;		

				case 'telegram':
					$form->edit->setHeader('Sambungkan Akun Telegram');
					
					$form->edit->addInput('telegram_id','hidden');
					$form->edit->input->telegram_id->addAttr('id="telegram_id"');
					
					$form->edit->addInput('telegram_data','hidden');
					$form->edit->input->telegram_data->addAttr('id="telegram_data"');

					$telegram_conf = $this->_tpl_model->config('notif_telegram');
					
					if ($id == $this->_tpl_model->user['id'] and !empty($telegram_conf['active'])) {
						$telegram_conf['data'] = @(array)json_decode($telegram_conf['data']);

						if (!empty($telegram_conf['data']['username'])) {
							$this->_tpl_model->js('controllers/admin/user_usr_telegram.js');
							$code = time().mt_rand();

							$form->edit->addInput('telegram_input','plaintext');
							$form->edit->input->telegram_input->setTitle('Akun Telegram');
							$form->edit->input->telegram_input->setValue('<input id="telegram_input" type="text" class="form-control" value="" title="Akun Telegram" placeholder="Akun Telegram" data-code="'.$code.'" data-url="https://api.telegram.org/bot'.$telegram_conf['token'].'/getUpdates" readonly>');

							$form->edit->addInput('telegram_instructions','plaintext');
							$form->edit->input->telegram_instructions->setTitle('Petunjuk');
							$form->edit->input->telegram_instructions->setValue('<ol><li>Klik <a href="https://t.me/'.$telegram_conf['data']['username'].'?start='.$code.'" target="_BLANK">Disini</a> untuk membuka Akun Telegram Kami.</li><li>Klik <b>Start</b> pada kotak pesan.</li><li>Tunggu sebentar dan lihat Akun Telegram Anda akan muncul diatas.</li><li>Lalu Simpan !</li></ol>');
						}
					}else{
						$form->edit->setSaveTool(false);
					}

					$input = 'telegram_data';
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

	function detail()
	{
		$id = @intval($_GET['id']);
		$this->_tpl_model->setLayout('blank');
		if ($id) {
			$form = $this->_pea_model->newForm('user');
			$form->initEdit('WHERE `id`='.$id);

			$form->edit->setHeader('User Detail');
			$form->edit->setModalResponsive();

			$form->edit->addInput('name', 'sqlplaintext');
			$form->edit->input->name->setTitle('Name');

			$form->edit->addInput('email', 'sqlplaintext');
			$form->edit->input->email->setTitle('Email');

			$form->edit->addInput('phone', 'sqlplaintext');
			$form->edit->input->phone->setTitle('Phone');

			$form->edit->addInput('image', 'file');
			$form->edit->input->image->setTitle('Image');
			$form->edit->input->image->setFolder('files/user/');
			$form->edit->input->image->setImageClick();
			$form->edit->input->image->setPlainText(true);

			$form->edit->addInput('gender', 'select');
			$form->edit->input->gender->setTitle('Gender');
			$form->edit->input->gender->addOption('Male', 1);
			$form->edit->input->gender->addOption('Female', 2);
			$form->edit->input->gender->setPlainText();

			$form->edit->addInput('birth_date', 'date');
			$form->edit->input->birth_date->setTitle('Birthdate');
			$form->edit->input->birth_date->setDateFormat('d M Y');
			$form->edit->input->birth_date->setPlainText();

			$form->edit->addInput('location_detail', 'sqlplaintext');
			$form->edit->input->location_detail->setTitle('Address');
			$form->edit->input->location_detail->setFieldName('CONCAT(`address`," ",`village_title`,", ",`district_title`,", ",`city_title`,", ",`province_title`,", ",`zip_code`)');
			
			$form->edit->setSaveTool(false);
			$form->edit->action();
			echo $form->edit->getForm();
		}	
		$this->_tpl_model->show();
	}

	function notif()
	{
		$this->_tpl_model->nav_add($this->_tpl_model->_url_current, 'Notification', 1);

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
	
		$form->initRoll($add_sql.' AND (`user_id`='.$this->_tpl_model->user['id'].' OR `group_id` IN('.implode($this->_tpl_model->user['group_ids']).') OR (`user_id`=0 AND `group_id`=0)) AND `type`=0 ORDER BY `id` DESC');

		$form->roll->addInput('created', 'datetime');
		$form->roll->input->created->setTitle('Date & Time');
		$form->roll->input->created->setPlainText();

		$form->roll->addInput('title', 'sqlplaintext');
		$form->roll->input->title->setTitle('Title');
		$form->roll->input->title->setDisplayFunction(function($value='', $id=0)
		{
			return '<a href="'.$this->_tpl_model->_url.'_Pea/notif_detail?id='.$id.'&type=0">'.$value.'</a>';
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

		$this->_tpl_model->show();
	}

	function address()
	{
		$form = $this->_pea_model->newForm('user_address');

		$form->initSearch();

		$form->search->addInput('keyword', 'keyword');
		$form->search->input->keyword->setTitle('Cari');
		$form->search->input->keyword->addSearchField('title,email,phone,province_title,city_title,district_title,village_title,address,zip_code');
		
		$form->search->addExtraField('user_id', $this->_tpl_model->user['id']);

		$add_sql = $form->search->action();
		$keyword = $form->search->keyword();
		
		echo $form->search->getForm();

		echo $this->_tpl_model->button('user/address_form', 'Tambah Alamat', 'fa fa-plus', 'modal_reload', 'style="margin-right: 10px; margin-bottom: 15px;"', 1);
		
		$form->initRoll($add_sql.' ORDER BY `id` DESC');

		$form->roll->addInput('title', 'sqllinks');
		$form->roll->input->title->setTitle('Judul');
		$form->roll->input->title->setLinks('user/address_form');
		$form->roll->input->title->setModal();
		$form->roll->input->title->setModalReload();
		$form->roll->input->title->setDisplayColumn();

		$form->roll->addInput('email', 'sqlplaintext');
		$form->roll->input->email->setTitle('Email');
		$form->roll->input->email->setDisplayColumn();

		$form->roll->addInput('phone', 'sqlplaintext');
		$form->roll->input->phone->setTitle('No HP');
		$form->roll->input->phone->setDisplayColumn();

		$form->roll->addInput('location_detail', 'sqlplaintext');
		$form->roll->input->location_detail->setTitle('Lokasi');
		$form->roll->input->location_detail->setFieldName('CONCAT(`address`," ",`village_title`,", ",`district_title`,", ",`city_title`,", ",`province_title`,", ",`zip_code`)');
		$form->roll->input->location_detail->setDisplayColumn();

		$form->roll->addInput('main', 'checkbox');
		$form->roll->input->main->setTitle('Alamat Utama');
		$form->roll->input->main->setCaption('ya');
		$form->roll->input->main->setDisplayColumn();
		$form->roll->input->main->setPlainText();

		$form->roll->addInput('created', 'datetime');
		$form->roll->input->created->setTitle('Dibuat');
		$form->roll->input->created->setPlainText();
		$form->roll->input->created->setDisplayColumn();

		$form->roll->addInput('updated', 'datetime');
		$form->roll->input->updated->setTitle('Diperbarui');
		$form->roll->input->updated->setPlainText();
		$form->roll->input->updated->setDisplayColumn(false);
		
		$form->roll->setRollDeleteCondition('{main} == 1');

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
		
		$form->edit->setHeader(!empty($id) ? 'Ubah Alamat' : 'Tambah Alamat');
		$form->edit->setModalResponsive();
		
		$form->edit->addInput('title','text');
		$form->edit->input->title->setTitle('Judul');
		$form->edit->input->title->setRequire();

		$form->edit->addInput('email','text');
		$form->edit->input->email->setTitle('Email');
		$form->edit->input->email->setType('email');
		$form->edit->input->email->setRequire();
		$form->edit->input->email->addAttr('id="i_email"');
		
		$form->edit->addInput('phone','text');
		$form->edit->input->phone->setTitle('No HP');
		$form->edit->input->phone->setType('tel');
		$form->edit->input->phone->setRequire();
		$form->edit->input->phone->addAttr('id="i_phone"');

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
			
		$form->edit->addInput('zip_code','text');
		$form->edit->input->zip_code->setTitle('Kode Pos');
		$form->edit->input->zip_code->setType('number');
		$form->edit->input->zip_code->setRequire();

		$form->edit->addInput('address', 'text');
		$form->edit->input->address->setTitle('Alamat');
		$form->edit->input->address->setRequire();
		$form->edit->input->address->addTip('Jln. Jendral Sudirman No.123 RT.05 RW.06');

		$form->edit->addInput('main', 'checkbox');
		$form->edit->input->main->setTitle('Alamat Utama');
		$form->edit->input->main->setCaption('ya');
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
}

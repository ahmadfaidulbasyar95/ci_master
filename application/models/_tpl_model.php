<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class _tpl_model extends CI_Model {

	public $name   = '';
	public $root   = '';
	public $url    = '';
	public $layout = '';

	public $_url         = '';
	public $_url_current = '';
	public $_root        = '';
	
	public $class  = '';
	public $method = '';
	public $task   = '';

	public $meta            = array();
	public $content         = '';
	public $config          = array();
	public $nav_list        = array();
	public $menu            = array();
	public $menu_unprotect  = array();
	public $user            = array();
	public $user_msg        = '';
	public $user_group_type = array(
		'Public' => 0,
		'Admin'  => 1,
	);
	
	private $ob_start = 1;

	function __construct()
	{
		parent::__construct();

		$this->load->model('_db_model');
		$this->load->library('session');

		$GLOBALS['tpl_includes'] = array(
			'js'  => array(),
			'css' => array(),
		);

		$this->_url         = base_url();
		$this->_url_current = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$this->_root        = FCPATH;

		$this->class  = $this->router->class;
		$this->method = $this->router->method;
		$this->task   = $this->router->directory.implode('/', $this->router->uri->rsegments);
		
		include_once $this->_root.'application/libraries/file.php';

		$this->setTemplate();

		$c = $this->config('site');
		$this->meta_title(@$c['meta_title']);
		$this->meta_description(@$c['meta_description']);
		$this->meta_keyword(@$c['meta_keyword']);
		$this->meta['domain'] = $c['domain'];
		$this->meta['icon']   = $this->_url.'files/uploads/'.$c['icon'];
		$this->nav_add($this->_url, '<i class="fa fa-home"></i> Home');

		if (!($this->class == 'dashboard' and in_array($this->method, ['index','main'])) and $this->class != '_T') {
			$menu = $this->_db_model->getRow('SELECT `id`,`position_id`,`title` FROM `menu` WHERE `active`=1 AND `url` LIKE "'.addslashes($this->task.($_GET ? '?'.http_build_query($_GET) : '')).'%" ORDER BY `url` LIMIT 1');
			if (!$menu) {
				$menu = $this->_db_model->getRow('SELECT `id`,`position_id`,`title` FROM `menu` WHERE `active`=1 AND `url` LIKE "'.addslashes($this->task).'%" ORDER BY `url` LIMIT 1');
			}
			if (!$menu) {
				$method_ = explode('_',$this->method);
				$method_ = (count($method_) == 1) ? '' : '/'.$method_[0];
				$menu    = $this->_db_model->getRow('SELECT `id`,`position_id`,`title` FROM `menu` WHERE `active`=1 AND `url` LIKE "'.addslashes(str_replace('/'.$this->method, $method_ , $this->task)).'%" ORDER BY `url` LIMIT 1');
			}
			if (!$menu) {
				$menu = $this->_db_model->getRow('SELECT `id`,`position_id`,`title` FROM `menu` WHERE `active`=1 AND `url` LIKE "'.addslashes(str_replace('/'.$this->method, '' , $this->task)).'%" ORDER BY `url` LIMIT 1');
			}
			if ($menu) {
				$this->meta_title($menu['title'].' | '.@$c['meta_title']);
				foreach ($this->menu_parent($menu['id'], $menu['position_id']) as $value) {
					if (!$value['url_type']) {
						$value['url'] = ($value['position_id']) ? $this->_url.$value['uri'].'.html' : $this->_url.$value['url'];
					}
					$this->nav_add($value['url'], $value['title']);
				}
			}
		}

		ob_start();
	}

	public function setTemplate($tpl = '', $bootstrap = 3)
	{
		if (empty($tpl)) {
			$tpl = $this->config('site', 'template');
		}
		$p = 'application/views/'.$tpl.'/';
		if (is_file($this->_root.$p.'index.php')) {
			$this->name = $tpl;
			$this->root = $this->_root.$p;
			$this->url  = $this->_url.$p;
			$this->setLayout();
			$GLOBALS['bsv'] = $bootstrap;
			return true;
		}
		return false;
	}
	public function setLayout($file = '')
	{
		if (empty($this->name)) {
			return false;
		}
		if (empty($file)) {
			$file = 'index';
		}
		if (is_file($this->root.$file.'.php')) {
			$this->layout = $file.'.php';
			return true;
		}
		return false;
	}
	public function view($file = '', $vars = array())
	{
		$__file__ = '';
		if (is_file($this->root.$file.'.php')) {
			$__file__ = $this->root.$file.'.php';
		}elseif (is_file($this->_root.'application/views/'.$file.'.php')) {
			$__file__ = $this->_root.'application/views/'.$file.'.php';
		}
		if ($__file__) {
			if (is_array($vars)) {
				if (isset($vars[0])) {
					$output = $vars;
				}else{
					foreach ($vars as $key => $value) {
						if (!isset($$key)) {
							$$key = $value;
						}
					}
				}
			}else{
				$output = $vars;
			}
			unset($vars);
			$tpl = $this;
			if (!$this->ob_start) {
				ob_start();
			}
			include $__file__;
		}
		$this->ob_start = 0;
		$this->content .= ob_get_clean();
	}
	public function show()
	{
		if ($this->ob_start) {
			$this->ob_start = 0;
			$this->content .= ob_get_clean();
		}
		$tpl = $this;
		include $this->root.$this->layout;
	}

	public function validateFile($file = '')
	{
		if ($file) {
			if (!filter_var($file, FILTER_VALIDATE_URL)) {
				if (is_file($file)) {
					$file = str_replace($this->_root, $this->_url, $file);
				}elseif (is_file($this->_root.'application/'.$file)) {
					$file = $this->_url.'application/'.$file;
				}elseif (is_file($this->root.$file)) {
					$file = $this->url.$file;
				}else{
					$file = '';
				}
			}
		}
		return $file;
	}
	public function css($file = '')
	{
		$file = $this->validateFile($file);
		if ($file) {
			if (!in_array($file, $GLOBALS['tpl_includes']['css'])) {
				$GLOBALS['tpl_includes']['css'][] = $file;
				echo '<link rel="stylesheet" href="'.$file.'">';
			}
		}
	}
	public function js($file = '')
	{
		$file = $this->validateFile($file);
		if ($file) {
			if (!in_array($file, $GLOBALS['tpl_includes']['js'])) {
				$GLOBALS['tpl_includes']['js'][] = $file;
				echo '<script src="'.$file.'"></script>';
			}
		}
	}

	public function lib($value='')
	{
		$p = $this->_root.'application/libraries/';
		if (is_file($p.$value.'.php')) {
			include_once $p.$value.'.php';
		}elseif (is_file($p.$value.'/'.$value.'.php')) {
			include_once $p.$value.'/'.$value.'.php';
		}else{
			die('libraries "'.$value.'" not found');
		}
	}

	public function config($name = '', $index = '')
	{
		$ret = ($index) ? '' : array();
		if ($name) {
			$dt = array();
			if (isset($this->config[$name])) {
				$dt = $this->config[$name];
			}else{
				$fl = $this->_root.'files/cache/config/'.$name.'.cfg';
				if (is_file($fl)) {
					$dt = json_decode(lib_file_read($fl), 1);
					if ($dt) {
						$this->config[$name] = $dt;
					}else{
						unlink($fl);
						return $this->config($name, $index);
					}
				}else{
					$dt = $this->_db_model->getOne('SELECT `value` FROM `config` WHERE `name`="'.addslashes($name).'"');
					if ($dt) {
						lib_file_write($fl, $dt);
						$dt = json_decode($dt, 1);
						if ($dt) {
							$this->config[$name] = $dt;
						}
					}
				}
			}
			if ($dt) {
				if ($index) {
					$ret = @$dt[$index];
				}else{
					$ret = $dt;
				}
			}
		}
		return $ret;
	}
	public function clean_cache()
	{
		$this->config = array();
		include_once $this->_root.'application/libraries/path.php';
		lib_path_delete($this->_root.'files/cache');
		$this->config('site');
		$this->config('dashboard');
	}

	public function menu($position_id = 0)
	{
		if (isset($this->menu[$position_id])) {
			$data = $this->menu[$position_id];
		}else{
			$data = $this->_db_model->getAll('SELECT * FROM `menu` WHERE `position_id`='.$position_id.' AND `active`=1 ORDER BY `orderby`');
			if ($data) {
				$data_ = array();
				foreach ($data as $value) {
					$data_[$value['par_id']][] = $value;
				}
				$data = $data_;
				$this->menu[$position_id] = $data;
			}
		}
		return $data;
	}
	public function menu_parent($menu_id = 0, $position_id = 0)
	{
		$data = array();
		foreach ($this->menu($position_id) as $value) {
			foreach ($value as $value1) {
				$data[$value1['id']] = $value1;
			}
		}
		$out = array();
		while (isset($data[$menu_id])) {
			$out[$menu_id] = $data[$menu_id];
			$menu_id       = $data[$menu_id]['par_id'];
		}
		return array_reverse($out);
	}
	public function menu_show($data = array(), $config_view = array())
	{
		if (is_numeric($data)) {
			$data = $this->menu($data);
		}
		$config_view_def = array(
			'wrap'     => '<ul class="nav navbar-nav">[menu]</ul>',
			'item'     => '<li><a href="[url]"><i class="[icon]"></i> [title]</a></li>',
			'item_sub' => '<li class="dropdown">
											<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="[icon]"></i> [title] <b class="caret"></b></a>
											<ul class="dropdown-menu">
												[submenu]
											</ul>
										</li>',
			'icon_def' => 'fa fa-fw fa-dot-circle',
		);
		$config_view = array_merge($config_view_def, $config_view);
		return str_replace('[menu]', $this->menu_show_item(0, $data, $config_view), $config_view['wrap']);
	}
	public function menu_show_item($par_id = 0, $data = array(), $config_view = array())
	{
		$output = '';
		if (isset($data[$par_id])) {
			foreach ($data[$par_id] as $value) {
				if ($this->user) {
					$menu_ids = ($value['position_id']) ? $this->user['menu_ids'][0] : $this->user['menu_ids'][1];
				}else{
					$menu_ids = array();
				}
				if (!$value['protect'] or ($value['protect'] and (in_array('all', $menu_ids) or in_array($value['id'], $menu_ids)))) {
					if (isset($data[$value['id']])) {
						$out              = $config_view['item_sub'];
						$value['submenu'] = $this->menu_show_item($value['id'], $data, $config_view);
					}else{
						$out = $config_view['item'];
					}
					if (!$value['url_type']) {
						if ($value['url'] == '/') {
							$value['url'] = $this->_url;
						}else{
							$value['url'] = ($value['position_id']) ? $this->_url.$value['uri'].'.html' : $this->_url.$value['url'];
						}
					}
					if (!$value['icon']) {
						$value['icon'] = $config_view['icon_def'];
					}
					foreach ($value as $key1 => $value1) {
						$out = str_replace('['.$key1.']', $value1, $out);
					}
					$output .= $out;
				}
			}
		}
		return $output;
	}
	public function menu_unprotect($method='')
	{
		if ($method) {
			$this->menu_unprotect[] = $method;
		}
	}

	public function meta()
	{
		echo '<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>'.@$this->meta['title'].'</title>
		<meta name="description" content="'.strip_tags(@$this->meta['description']).'">
		<meta name="keywords" content="'.strip_tags(@$this->meta['keyword']).'">
		<meta name="developer" content="AFB">
		<meta name="Author" content="'.$this->meta['domain'].'">
		<meta name="ROBOTS" content="all, index, follow">
		<link rel="shortcut icon" type="image/x-icon" href="'.$this->meta['icon'].'">
		<script type="text/javascript">var _ROOT="/";var _URL="'.$this->_url.'";</script>'.@$this->meta['add'];
	}
	public function meta_title($value = '', $is_add = 0)
	{
		if ($value) {
			if ($is_add) {
				$this->meta['title'] = $value.'|'.@$this->meta['title'];
			}else{
				$this->meta['title'] = $value;
			}
		}
	}
	public function meta_description($value = '', $is_add = 0)
	{
		if ($value) {
			if ($is_add) {
				$this->meta['description'] = $value.'. '.@$this->meta['description'];
			}else{
				$this->meta['description'] = $value;
			}
		}
	}
	public function meta_keyword($value = '', $is_add = 0)
	{
		if ($value) {
			if ($is_add) {
				$this->meta['keyword'] = $value.', '.@$this->meta['keyword'];
			}else{
				$this->meta['keyword'] = $value;
			}
		}
	}
	public function meta_add($value = '')
	{
		if ($value) {
			$this->meta['add'] = @$this->meta['add'].$value;
		}
	}

	public function nav_add($link = '', $text = '', $index = 'AUTO')
	{
		if (!$text) {
			$text = $link;
			$link = '';
		}
		if ($link) {
			if (!filter_var($link, FILTER_VALIDATE_URL)) {
				$link = $this->_url.$link;
			}
		}
		$dt = array(
			'text' => $text,
			'link' => $link,
		);
		if ($index == 'AUTO') {
			$this->nav_list[] = $dt;
		}else{
			$this->nav_list[$index] = $dt;
		}
	}
	public function nav_show()
	{
		$ret = '<ol class="breadcrumb">';
		foreach ($this->nav_list as $value) {
			if ($value['link']) {
				$ret .= '<li><a href="'.$value['link'].'">'.$value['text'].'</a></li>';
			}else{
				$ret .= '<li class="active">'.$value['text'].'</li>';
			}
		}
		$ret .= '</ol>';
		return $ret;
	}

	public function user_msg($value = '')
	{
		if ($value) {
			$this->user_msg = $value;
		}else{
			return $this->user_msg;
		}
	}
	public function user_login($usr = '',$pwd = '', $type = 0, $remember = 0)
	{
		if ($usr and $pwd) {
			$data = $this->_db_model->getRow('SELECT * FROM `user` WHERE `username`="'.addslashes($usr).'"');
			if ($data) {
				if (!$data['active']) {
					$this->user_msg('Your account has been blocked');
					return false;
				}
				$data['group_ids']  = @(array)json_decode($data['group_ids']);
				$data['group_data'] = array();
				if ($data['group_ids']) {
					$data['group_data'] = $this->_db_model->getAll('SELECT * FROM `user_group` WHERE `id` IN('.implode(',', $data['group_ids']).')');
				}
				$allowed          = 0;
				$data['menu_ids'] = array();
				foreach ($this->user_group_type as $value) {
					$data['menu_ids'][$value] = array();
				}
				foreach ($data['group_data'] as $key => $value) {
					$data['group_data'][$key]['menu_ids'] = @(array)json_decode($value['menu_ids']);
					if ($value['type'] == $type) {
						$allowed = 1;
					}
					$data['menu_ids'][$value['type']] = array_merge($data['menu_ids'][$value['type']], $data['group_data'][$key]['menu_ids']);
				}
				if (!$allowed) {
					$this->user_msg('Your account does not have access on this page');
					return false;
				}
				$this->load->model('_encrypt_model');
				$pwd_current = $this->_encrypt_model->decode($data['password']);
				if ($pwd == $pwd_current) {
					$_SESSION['user_login'][$type] = $data;
					if ($remember) {
						$exp = intval($this->config('user', 'login_remember'))*60;
						setcookie('ULBWQPHGF'.$type.'VCN', $this->_encrypt_model->encodeToken($usr.'||'.$pwd, $exp), time() + $exp * 60, '/');
					}else{
						setcookie('ULBWQPHGF'.$type.'VCN', '', time() - 3600, '/');
					}
					return true;
				}
			}
		}
		$this->user_msg('Invalid Username or Password');
		return false;
	}
	public function user_login_remember($type = 0)
	{
		if (isset($_COOKIE['ULBWQPHGF'.$type.'VCN'])) {
			if (isset($_SESSION['user_login_remember_false-'.$type])) {
				unset($_SESSION['user_login_remember_false-'.$type]);
			}else{
				$this->load->model('_encrypt_model');
				$dt = $this->_encrypt_model->decodeToken($_COOKIE['ULBWQPHGF'.$type.'VCN']);
				if ($dt) {
					$dt  = explode('||', $dt);
					$ret = $this->user_login($dt[0], $dt[1], $type, 1);
					if ($ret) {
						return $ret;
					}
				}
			}
			setcookie('ULBWQPHGF'.$type.'VCN', '', time() - 3600, '/');
		}
		return false;
	}
	public function user_pwd_validate($pwd = '', $type = 0)
	{
		if (!empty($_SESSION['user_login'][$type])) {
			$this->load->model('_encrypt_model');
			$pwd_current = $this->_encrypt_model->decode($_SESSION['user_login'][$type]['password']);
			if ($pwd == $pwd_current) {
				return true;
			}
		}
		return false;
	}
	public function user_login_validate($type = 0)
	{
		if (empty($_SESSION['user_login'][$type])) {
			if ($type == 1) {
				if (isset($_COOKIE['ULBWQPHGF'.$type.'VCN'])) {
					redirect($this->_url.$this->config('dashboard', 'login_uri'));
				}else{
					show_error('Please Sign In', 401, '401 Unauthorized');
				}
			}else{
				$_SESSION['user_return'] = $this->_url_current;
				$_SESSION['user_post']   = $_POST;
				redirect($this->_url.'user/login');
			}
		}else{
			$this->user = $_SESSION['user_login'][$type];
			$allowed    = 0;
			foreach ($this->user['group_data'] as $value) {
				if ($value['type'] == $type) {
					$allowed = 1;
				}
			}
			if ($allowed) {
				if (!in_array('all', $this->user['menu_ids'][$type]) and !in_array($this->method, $this->menu_unprotect)) {
					$menu = $this->_db_model->getCol('SELECT `id` FROM `menu` WHERE `type`='.$type.' AND `protect`=1 AND `active`=1 AND `url` LIKE "'.addslashes($this->task.($_GET ? '?'.http_build_query($_GET) : '')).'%"');
					if (!$menu) {
						$menu = $this->_db_model->getCol('SELECT `id` FROM `menu` WHERE `type`='.$type.' AND `protect`=1 AND `active`=1 AND `url` LIKE "'.addslashes($this->task).'%"');
					}
					if (!$menu) {
						$method_ = explode('_',$this->method);
						$method_ = (count($method_) == 1) ? '' : '/'.$method_[0];
						$menu    = $this->_db_model->getCol('SELECT `id` FROM `menu` WHERE `type`='.$type.' AND `protect`=1 AND `active`=1 AND `url` LIKE "'.addslashes(str_replace('/'.$this->method, $method_ , $this->task)).'%"');
					}
					if (!$menu) {
						$menu = $this->_db_model->getCol('SELECT `id` FROM `menu` WHERE `type`='.$type.' AND `protect`=1 AND `active`=1 AND `url` LIKE "'.addslashes(str_replace('/'.$this->method, '', $this->task)).'%"');
					}
					if ($menu) {
						$allowed = 0;
						foreach ($menu as $menu_id) {
							if (in_array($menu_id, $this->user['menu_ids'][$type])) {
								$allowed = 1;
							}
						}
					}
				}
			}
			if (!$allowed) {
				show_error('Your account does not have access on this page', 401, '401 Unauthorized');
			}
			$this->user['image'] = $this->validateFile($this->_root.'files/user/thumb/'.$this->user['image']);
			if (!$this->user['image']) {
				$this->user['image'] = $this->_url.'files/uploads/'.$this->config('user', 'img_def');
			}
			if (isset($_SESSION['user_post_load'])) {
				$_POST = $_SESSION['user_post_load'];
				unset($_SESSION['user_post_load']);
			}
			return $this->user;
		}
	}
	public function user_login_with($provider = '', $type = 0)
	{
		$email = '';
		switch ($provider) {
			case 'google':
				if (isset($_SESSION['user_login_google'])) {
					$email = $_SESSION['user_login_google']['email'];
					unset($_SESSION['user_login_google']);
				}else{
					redirect($this->_url.'_T/login_google?return='.urlencode($this->_url_current));
				}
				break;
			
			default:
				show_error('Login Provider Unavailable', 401, '401 Unauthorized');
				break;
		}
		if ($email) {
			$dt = $this->_db_model->getRow('SELECT `username`,`password` FROM `user` WHERE `email`="'.addslashes($email).'"');
			if ($dt) {
				$this->load->model('_encrypt_model');
				return $this->user_login($dt['username'], $this->_encrypt_model->decode($dt['password']), $type);
			}else{
				return $this->user_login('', '', $type);
			}
		}
	}
	public function user_forget_pwd($search = '', $provider = '', $code = '')
	{
		if (isset($_SESSION['user_forget_pwd'])) {
			if ($code and !isset($_SESSION['user_forget_pwd_success'])) {
				if ($code == $_SESSION['user_forget_pwd']['code']) {
					switch ($_SESSION['user_forget_pwd']['provider']) {
						case 'email':
							$this->load->model('_encrypt_model');
							$password = substr($this->_encrypt_model->encode($code), 0, 12);
							$this->load->model('_notif_model');
							$this->_notif_model->sendEmail('forget_password_success', $_SESSION['user_forget_pwd']['dt']['email'], array(
								'password' => $password
							));
							$this->_db_model->update('user', array('password' => $this->_encrypt_model->encode($password)), $_SESSION['user_forget_pwd']['dt']['id']);
							$_SESSION['user_forget_pwd_success'] = 1;
							break;

						case 'phone':
							$this->load->model('_encrypt_model');
							$password = substr($this->_encrypt_model->encode($code), 0, 12);
							$this->load->model('_notif_model');
							$this->_notif_model->sendWA('forget_password_success', $_SESSION['user_forget_pwd']['dt']['phone'], array(
								'password' => $password
							));
							$this->_db_model->update('user', array('password' => $this->_encrypt_model->encode($password)), $_SESSION['user_forget_pwd']['dt']['id']);
							$_SESSION['user_forget_pwd_success'] = 1;
							break;
						
						default:
							show_error('Forget Password Provider Unavailable', 401, '401 Unauthorized');
							break;
					}
				}else{
					$this->user_msg('Invalid code');
				}
			}
			return $_SESSION['user_forget_pwd']['dt'];
		}else{
			if ($search) {
				$dt = $this->_db_model->getRow('SELECT `id`,`name`,`image`,`email`,`phone` FROM `user` WHERE `username`="'.addslashes($search).'" OR `email`="'.addslashes($search).'" OR `phone`="'.addslashes($search).'"');
				if ($dt) {
					$dt['search'] = $search;
					$dt['image']  = $this->validateFile($this->_root.'files/user/thumb/'.$dt['image']);
					if (!$dt['image']) {
						$dt['image'] = $this->_url.'files/uploads/'.$this->config('user', 'img_def');
					}
					if ($provider) {
						switch ($provider) {
							case 'email':
								$_SESSION['user_forget_pwd'] = array(
									'dt'       => $dt,
									'code'     => mt_rand(100000,999999),
									'provider' => 'email',
								);
								$this->load->model('_notif_model');
								$this->_notif_model->sendEmail('forget_password', $dt['email'], array(
									'code' => $_SESSION['user_forget_pwd']['code']
								));
								break;

							case 'phone':
								$_SESSION['user_forget_pwd'] = array(
									'dt'       => $dt,
									'code'     => mt_rand(100000,999999),
									'provider' => 'phone',
								);
								$this->load->model('_notif_model');
								$this->_notif_model->sendWA('forget_password', $dt['phone'], array(
									'code' => $_SESSION['user_forget_pwd']['code']
								));
								break;
							
							default:
								show_error('Forget Password Provider Unavailable', 401, '401 Unauthorized');
								break;
						}
					}
					return $dt;
				}else{
					$this->user_msg('Account not found');
				}
			}
		}
	}
	public function user_logout($type = 0)
	{
		if (isset($_SESSION['user_login'][$type])) {
			unset($_SESSION['user_login'][$type]);
			if (isset($_COOKIE['ULBWQPHGF'.$type.'VCN'])) {
				$_SESSION['user_login_remember_false-'.$type] = 1;
			}
		}
	}
	public function user_device()
	{
		$this->load->library('user_agent');

		if ($this->agent->is_browser()) {
			$agent = $this->agent->browser().' '.$this->agent->version();
		}elseif ($this->agent->is_robot()) {
			$agent = $this->agent->robot();
		}elseif ($this->agent->is_mobile()) {
			$agent = $this->agent->mobile();
		}else{
			$agent = 'Unidentified User Agent';
		}

		return $this->agent->platform().' '.$agent;
	}

	public function button($link = '', $text = '', $icon = 'fa fa-send', $cls = '', $attr = '', $use_modal = 0)
	{
		if (!$link and @$_GET['return']) {
			$link = $_GET['return'];
			$icon = 'fa fa-chevron-left';
		}
		if ($link) {
			if (!filter_var($link, FILTER_VALIDATE_URL)) {
				$link = $this->_url.$link;
			}
			if ($use_modal) {
				$js = 'modal.min';
				if (!in_array($js, @(array)$GLOBALS['pea_includes_load']['js'])) {
					$GLOBALS['pea_includes_load']['js'][] = $js;
					echo '<script src="'.$this->_url.'application/libraries/pea/includes/'.$js.'.js"></script>';
				}
				return '<a href="'.$link.'" class="btn btn-default modal_processing '.$cls.'" '.$attr.'><i class="'.$icon.'"></i> '.$text.'</a>';
			}else{
				return '<a href="http://" class="btn btn-default '.$cls.'" onclick=\'window.location.href="'.$link.'"\' '.$attr.'><i class="'.$icon.'"></i> '.$text.'</a>';
			}
		}
		return '';
	}
	public function msg($text = '', $type = 'info')
	{
		$icon = array(
			'success' => 'fa fa-check-circle',
			'info'    => 'fa fa-info-circle',
			'warning' => 'fa fa-warning',
			'danger'  => 'fa fa-times-circle',
		);
		return '<div class="alert alert-'.$type.'" role="alert">
			<i class="'.@$icon[$type].'"></i> '.$text.'
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>';
	}
}

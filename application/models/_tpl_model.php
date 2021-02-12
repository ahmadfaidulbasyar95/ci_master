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

		$this->_url         = base_url();
		$this->_url_current = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$this->_root        = FCPATH;

		$this->class  = $this->router->class;
		$this->method = $this->router->method;
		$this->task   = $this->router->uri->uri_string;
		
		include_once $this->_root.'application/libraries/file.php';

		$this->setTemplate();

		$c = $this->config('site');
		$this->meta_title(@$c['meta_title']);
		$this->meta_description(@$c['meta_description']);
		$this->meta_keyword(@$c['meta_keyword']);
		$this->meta['domain'] = $c['domain'];
		$this->meta['icon']   = $this->_url.'files/uploads/'.$c['icon'];
		$this->nav_add($this->_url, '<i class="fa fa-home"></i> Home');

		if (!($this->class == 'dashboard' and in_array($this->method, ['index','main']))) {
			$menu = $this->_db_model->getRow('SELECT `id`,`position_id`,`title` FROM `menu` WHERE `active`=1 AND `url` LIKE "'.addslashes($this->task.($_GET ? '?'.http_build_query($_GET) : '')).'%" LIMIT 1');
			if (!$menu) {
				$menu = $this->_db_model->getRow('SELECT `id`,`position_id`,`title` FROM `menu` WHERE `active`=1 AND `url` LIKE "'.addslashes($this->task).'%" LIMIT 1');
			}
			if ($menu) {
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
			$tpl = 'default';
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
			echo '<link rel="stylesheet" href="'.$file.'">';
		}
	}
	public function js($file = '')
	{
		$file = $this->validateFile($file);
		if ($file) {
			echo '<script src="'.$file.'"></script>';
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
		include_once $this->_root.'application/libraries/path.php';
		lib_path_delete($this->_root.'files/cache');
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
			'wrap'     => '<ul class="nav navbar-nav navbar-right">[menu]</ul>',
			'item'     => '<li><a href="[url]">[title]</a></li>',
			'item_sub' => '<li class="dropdown">
											<a href="#" class="dropdown-toggle" data-toggle="dropdown">[title] <b class="caret"></b></a>
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
				if (isset($data[$value['id']])) {
					$out              = $config_view['item_sub'];
					$value['submenu'] = $this->menu_show_item($value['id'], $data, $config_view);
				}else{
					$out = $config_view['item'];
				}
				if (!$value['url_type']) {
					$value['url'] = ($value['position_id']) ? $this->_url.$value['uri'].'.html' : $this->_url.$value['url'];
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
		return $output;
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
			$text = $this->meta['title'];
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
				$ret .= '<li class="active">'.$text.'</li>';
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
	public function user_login($usr = '',$pwd = '', $type = 0)
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
					return true;
				}
			}
		}
		$this->user_msg('Invalid Username or Password');
		return false;
	}
	public function user_login_validate($type = 0)
	{
		if (empty($_SESSION['user_login'][$type])) {
			show_error('Please Sign In', 401, '401 Unauthorized');
		}else{
			$this->user = $_SESSION['user_login'][$type];
			$allowed    = 0;
			foreach ($this->user['group_data'] as $value) {
				if ($value['type'] == $type) {
					$allowed = 1;
				}
			}
			if ($allowed) {
				if (!in_array('all', $this->user['menu_ids'][$type])) {
					$menu = $this->_db_model->getCol('SELECT `id` FROM `menu` WHERE `type`='.$type.' AND `protect`=1 AND `active`=1 AND `url` LIKE "'.addslashes($this->_tpl_model->task.($_GET ? '?'.http_build_query($_GET) : '')).'%"');
					if (!$menu) {
						$menu = $this->_db_model->getCol('SELECT `id` FROM `menu` WHERE `type`='.$type.' AND `protect`=1 AND `active`=1 AND `url` LIKE "'.addslashes($this->_tpl_model->task).'%"');
					}
					if (!$menu) {
						$menu = $this->_db_model->getCol('SELECT `id` FROM `menu` WHERE `type`='.$type.' AND `protect`=1 AND `active`=1 AND `url` LIKE "'.addslashes(str_replace('/'.$this->_tpl_model->method, '', $this->_tpl_model->task)).'%"');
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
			return $this->user;
		}
	}
	public function user_logout($type = 0)
	{
		if (isset($_SESSION['user_login'][$type])) {
			unset($_SESSION['user_login'][$type]);
		}
	}

	public function button($link = '', $text = '', $icon = 'fa fa-send', $cls = '', $attr = '')
	{
		if (!$link and @$_GET['return']) {
			$link = $_GET['return'];
			$icon = 'fa fa-chevron-left';
		}
		if ($link) {
			if (!filter_var($link, FILTER_VALIDATE_URL)) {
				$link = $this->_url.$link;
			}
			return '<a href="http://" class="btn btn-default '.$cls.'" onclick=\'window.location.href="'.$link.'"\' '.$attr.'><i class="'.$icon.'"></i> '.$text.'</a>';
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

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

	public $meta     = array();
	public $content  = '';
	public $config   = array();
	public $nav_list = array();
	public $menu     = array();
	
	private $ob_start = 1;

	function __construct()
	{
		include_once $this->_root.'application/libraries/file.php';
		
		parent::__construct();

		$this->load->model('_db_model');

		$this->_url         = base_url();
		$this->_url_current = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$this->_root        = FCPATH;

		$this->setTemplate();

		$c = $this->config('site');
		$this->meta_title(@$c['meta_title']);
		$this->meta_description(@$c['meta_description']);
		$this->meta_keyword(@$c['meta_keyword']);
		$this->meta['domain'] = $c['domain'];
		$this->meta['icon']   = $this->_url.'files/uploads/'.$c['icon'];
		$this->nav_add($this->_url, '<i class="fa fa-home"></i> Home');

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

	public function button($link = '', $text = '', $icon = 'fa fa-send')
	{
		if (!$link and @$_GET['return']) {
			$link = $_GET['return'];
			$icon = 'fa fa-chevron-left';
		}
		if ($link) {
			if (!filter_var($link, FILTER_VALIDATE_URL)) {
				$link = $this->_url.$link;
			}
			return '<a href="http://" class="btn btn-default" onclick=\'window.location.href="'.$link.'"\'><i class="'.$icon.'"></i> '.$text.'</a>';
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

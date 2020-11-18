<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class _tpl_model extends CI_Model {

	public $name   = '';
	public $root   = '';
	public $url    = '';
	public $layout = '';

	public $_url  = '';
	public $_root = '';

	public $meta    = array();
	public $content = '';
	public $config  = array();
	
	private $ob_start = 1;

	function __construct()
	{
		include_once $this->_root.'application/libraries/file.php';
		
		parent::__construct();

		$this->load->model('_db_model');

		$this->_url  = base_url();
		$this->_root = FCPATH;

		$this->setTemplate();

		$c = $this->config('site');
		$this->meta_title(@$c['meta_title']);
		$this->meta_description(@$c['meta_description']);
		$this->meta_keyword(@$c['meta_keyword']);
		$this->meta['domain'] = $c['domain'];
		$this->meta['icon']   = $this->_url.'files/uploads/'.$c['icon'];

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

	public function validateUrl($file = '')
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
		$file = $this->validateUrl($file);
		if ($file) {
			echo '<link rel="stylesheet" href="'.$file.'">';
		}
	}

	public function js($file = '')
	{
		$file = $this->validateUrl($file);
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
}

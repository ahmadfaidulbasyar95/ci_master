<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class _tpl_model extends CI_Model {

	public $name   = '';
	public $root   = '';
	public $url    = '';
	public $layout = '';

	public $_url  = '';
	public $_root = '';

	public $content  = '';
	
	private $ob_start = 1;

	function __construct()
	{
		parent::__construct();

		$this->_url  = base_url();
		$this->_root = FCPATH;

		$this->setTemplate();

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
			$file = 'index.php';
		}
		if (is_file($this->root.$file)) {
			$this->layout = $file;
			return true;
		}
		return false;
	}

	public function view($file = '', $vars = array())
	{
		$__file__ = '';
		if (is_file($this->root.$file.'.php')) {
			$__file__ = $this->root.$file;
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
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once '_function.php';

class MY_Controller extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		@session_start();
		ob_start();
		$this->load->model('_user_model');
		$this->load->model('_config_model');
		
		$APPURL = $this->_config_model->get('site', 'url');
		if (!$APPURL) {
			$APPURL = preg_replace('~\/$~', '', @$this->config->config['base_url']);
			$this->_config_model->set('site', 'url', $APPURL);
		}
		define('APPURL', $APPURL.'/');
		unset($APPURL);

		$this->_uri = array_values($this->uri->rsegments); 
		
		$TEMPLATE = $this->_config_model->get('site','template');
		if (!$TEMPLATE) {
			$TEMPLATE = 'default';
			$this->_config_model->set('site','template', $TEMPLATE);
		}
		$this->setTemplate($TEMPLATE);
		unset($TEMPLATE);
	}

	function __destruct()
	{
		$this->_content = ob_get_clean();
		if (@$this->_template['layout']) {
			include $this->_template['path'].$this->_template['layout'].'.php';
		}else{
			show_error('Invalid Template Path');
		}
	}

	public function contentView($method = '')
	{
		if ($method) {
			if (@$this->_template['layout']) {
				$file = $this->_template['path'].'controllers'.$this->_controller['parent'].'/'.$this->_controller['name'].'/'.$method.'.php';
				if (is_file($file)) include $file;
				else{
					$file = APPPATH.'views/controllers'.$this->_controller['parent'].'/'.$this->_controller['name'].'/'.$method.'.php';
					if (is_file($file)) include $file;
				}
			}
		}
	}

	public function contentShow()
	{
		echo $this->_content;
	}

	public function setTemplate($_template = '')
	{
		if ($_template) {
			if (file_exists(APPPATH.'views/templates/'.$_template)) {
				$this->_template = array(
					'name'   => $_template,
					'path'   => APPPATH.'views/templates/'.$_template.'/',
					'url'    => APPURL.'application/views/templates/'.$_template.'/',
				);
				$this->setLayout();
			}
		}
	}
	public function setLayout($layout = 'index')
	{
		if (isset($this->_template['path'])) {
			if (is_file($this->_template['path'].$layout.'.php')) {
				$this->_template['layout'] = $layout;
			}
		}
	}

	public function setController($file = '', &$method = '', &$params = array(), $is_sub = '')
	{
		if (is_file($file)) {
			$path = str_replace('.php', '/', $file);
			if ($is_sub) {
				if (is_file($path.$method.'.php')) $is_sub = '';
			}
			if ($is_sub) {
				$is_sub = basename($file,'.php');
				$name   = ucfirst($method);
				if (isset($params[0])) {
					$method = $params[0];
					unset($params[0]);
					$params = array_values($params);
				}else $method = 'index';
				$path .= $name.'/';
				$file  = str_replace('.php', '/'.$name.'.php', $file);
			}
			$this->_controller = array(
				'name'   => basename($file,'.php'),
				'path'   => $path,
				'url'    => str_replace([APPPATH.'controllers/','.php'], [APPURL,'/'], $file),
				'file'   => (is_file($path.$method.'.php')) ? $path.$method.'.php' : '',
				'parent' => ($is_sub) ? '/'.$is_sub : '',
			);
		}
	}

}

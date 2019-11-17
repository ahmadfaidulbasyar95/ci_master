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
		
		$TEMPLATE = $this->_config_model->get('site','template');
		if (!$TEMPLATE) {
			$TEMPLATE = 'admin';
			$this->_config_model->set('site','template', $TEMPLATE);
		}
		$this->setTemplate($TEMPLATE);
		unset($TEMPLATE);

		$CONTROLLER = array_values($this->uri->rsegments); 
		if (@$CONTROLLER[0] == 'admin') {
			$this->_controller = array(
				'name' => ucfirst(@$CONTROLLER[1]),
				'path' => APPPATH.'controllers/'.ucfirst(@$CONTROLLER[1]).'/admin/',
				'url'  => APPURL.'admin/'.ucfirst(@$CONTROLLER[1]).'/',
			);
		}else{
			$this->_controller = array(
				'name' => ucfirst(@$CONTROLLER[0]),
				'path' => APPPATH.'controllers/'.ucfirst(@$CONTROLLER[0]).'/',
				'url'  => APPURL.ucfirst(@$CONTROLLER[0]).'/',
			);
		}
	}

	function __destruct()
	{
		$this->_content = ob_get_clean();
		echo $this->_content;
	}

	public function setTemplate($_template = '')
	{
		if ($_template) {
			if (file_exists(APPPATH.'views/'.$_template)) {
				$this->_template = array(
					'name' => $_template,
					'path' => APPPATH.'views/'.$_template.'/',
					'url'  => APPURL.'application/views/'.$_template.'/',
				);
			}
		}
	}

}

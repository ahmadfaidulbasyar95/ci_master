<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once '_function.php';

class MY_Controller extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		@session_start();
		ob_start();
	}

	function __destruct()
	{
		$this->_content = ob_get_clean();
		echo $this->_content;
	}

}

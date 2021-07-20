<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/../../path.php';
include_once __DIR__.'/text.php';
class lib_pea_frm_captcha extends lib_pea_frm_text
{	
	public $captcha_data      = array();
	public $captcha_data_load = array();
	public $captcha_opt       = array();

	function __construct($opt, $name)
	{
		if ($name != 'captcha') {
			die('PEA::FORM "captcha" input name must also use captcha');
		}

		parent::__construct($opt, $name);
		
		if (!in_array($this->init, ['add','edit'])) {
			die('PEA::FORM "captcha" only use in PEA::EDIT');
		}

		$this->setPlainText();
		$this->setFieldName();
		$this->setType('captcha');
		
		if (!empty($_SESSION['pea_captcha'][$this->name])) {
			$this->captcha_data_load = $_SESSION['pea_captcha'][$this->name];
		}
	}

	function setOpt($index = '', $value = '')
	{
		if ($index) $this->captcha_opt[$index] = $value;
	}

	function getPostValue($index = '')
	{
		$this->msg = str_replace('{msg}', 'Captcha Invalid', $this->failMsgTpl);
		if (!empty($this->captcha_data_load) and !empty($_POST[$this->name])) {
			if (strtolower($this->captcha_data_load['word']) == strtolower($_POST[$this->name])) {
				$this->msg = '';
			}
		}
	}

	public function getForm($index = '', $values = array())
	{
		if (is_file($this->_root.'system/helpers/captcha_helper.php')) {
			include_once $this->_root.'system/helpers/captcha_helper.php';
			$cap = array(
				'img_path' => $this->_root.'files/cache/captcha/',
				'img_url'  => $this->_url.'files/cache/captcha/',
			);
			lib_path_create($cap['img_path']);
			$this->captcha_data = create_captcha(array_merge($this->captcha_opt,$cap));
		}
		if (empty($this->captcha_data)) {
			die('PEA::FORM "captcha" Create Captcha Failed');
		}else{
			$this->addTip($this->captcha_data['image'].'<input type="text" name="'.$this->name.'" class="form-control" value="" required="required" style="width: 150px;">');
			$_SESSION['pea_captcha'][$this->name] = $this->captcha_data;
		}

		return parent::getForm($index = '', $values = array());
	}

}
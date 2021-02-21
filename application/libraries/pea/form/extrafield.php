<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/text.php';
class lib_pea_frm_extrafield extends lib_pea_frm_text
{	
	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->setInputPosition('hidden');
	}

	public function getPostValue($index = '')
	{
		return $this->defaultValue;
	}

	public function getValue($index = '')
	{
		return $this->defaultValue;
	}

	public function getForm($index = '', $values = array())
	{
		return '';
	}
}
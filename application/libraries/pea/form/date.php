<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/datetime.php';
class lib_pea_frm_date extends lib_pea_frm_datetime
{	
	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->setDateFormat('d M Y');
		$this->setDateFormatInput('DD MMM YYYY');
		$this->setDateConfig('timePicker', false);
	}
}
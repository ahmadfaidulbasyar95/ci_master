<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/datetimeinterval.php';
class lib_pea_frm_dateinterval extends lib_pea_frm_datetimeinterval
{	

	function __construct($opt, $name)
	{
		parent::__construct($opt, $name);
		$this->setDateFormatInput('DD MMM YYYY');
		$this->setDateFormat('d M Y');
		$this->setDateConfig('timePicker', false);
	}
}
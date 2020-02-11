<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (@$params[0] and @$params[1]) 
{
	$reportPath = $this->_root.'application/cache/report/';
	$reportData = array();
	if (is_file($reportPath.$params[1].'.json')) {
		$reportData = json_decode(file_get_contents($reportPath.$params[1].'.json'), 1);
	}

	if ($reportData) 
	{
		switch ($params[0]) {
			case 'json':
				include_once dirname(__FILE__).'/../../output.php';
				lib_output_json($reportData);
				break;

			case 'excel':
				include_once dirname(__FILE__).'/../../excel/excel.php';
				
				break;
		}
	}
}
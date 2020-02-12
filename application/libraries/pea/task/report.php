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
				if (isset($reportData[0])) {
					$reportData = array('Data' => $reportData);
				}
				foreach ($reportData as $key => $value) {
					$reportData[$key] = array_merge(array(array_keys(array_values($value)[0])), $value);
				}
				$excel = new lib_excel();
				$excel->create($reportData)->download();
				break;

			case 'pdf':
				include_once dirname(__FILE__).'/../../pdf/pdf.php';
				$pdf = new lib_pdf();
				$pdf->createTable($reportData, array_keys(array_values($reportData)[0]))->Output();
				break;

			case 'table':
				include_once dirname(__FILE__).'/../../table.php';
				echo '<style>'.file_get_contents(dirname(__FILE__).'/../../pdf/table.css').'</style>';
				echo lib_table($reportData, array_keys(array_values($reportData)[0]));
				break;
		}
	}
}
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
			case 'excel':
				include_once __DIR__.'/../../excel/excel.php';
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
				include_once __DIR__.'/../../pdf/pdf.php';
				$header = array_keys(array_values($reportData)[0]);
				$pdf    = new lib_pdf((count($header) > 4) ? 'L' : 'P');
				$pdf->createTable($reportData, $header)->Output();
				break;

			case 'html':
				include_once __DIR__.'/../../table.php';
				echo '<style>'.file_get_contents(__DIR__.'/../../pdf/table.css').'</style>';
				echo lib_table($reportData, array_keys(array_values($reportData)[0]));
				break;

			case 'json':
				include_once __DIR__.'/../../output.php';
				lib_output_json($reportData);
				break;
		}
	}
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!defined('PDF_ROOT'))
	define('PDF_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);

require_once PDF_ROOT.'lib'.DIRECTORY_SEPARATOR.'tcpdf.php';
class lib_pdf extends TCPDF
{
	function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false)
	{
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
	}

	function createTable($data = array(), $header = array(), $title='')
	{
		include_once dirname(__FILE__).'/../table.php';

		$html  = '<style>'.file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'table_def.css').file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'table.css').'</style>';
		$html .= lib_table($data, $header, $title);
		
		$this->AddPage();
		$this->writeHTML($html, true, false, true, false, '');
		return $this;
	}

	function Output($name='doc.pdf', $dest='I')
	{
		if (empty($name)) {
			$name = 'Pdf-'.date('Y-m-d').'.pdf';
		}
		return parent::Output($name, $dest);
	}
}
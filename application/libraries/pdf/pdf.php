<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!defined('PDF_ROOT'))
	define('PDF_ROOT', __DIR__ . DIRECTORY_SEPARATOR);

require_once PDF_ROOT.'lib'.DIRECTORY_SEPARATOR.'autoload.inc.php';
use Dompdf\Dompdf;
class lib_pdf extends Dompdf
{
	function __construct($orientation = 'P', $format = 'A4')
	{
		parent::__construct();
		$this->setPaper($format, ($orientation == 'P') ? 'portrait' : 'landscape');
	}

	function createTable($data = array(), $header = array(), $title='')
	{
		include_once __DIR__.'/../table.php';

		$html  = '<style>'.file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'table.css').'</style>';
		$html .= lib_table($data, $header, $title);
		
		$this->loadHtml($html);
		return $this;
	}

	function Output($name = '')
	{
		if (empty($name)) {
			$name = 'Pdf-'.date('Y-m-d').'.pdf';
		}
		$this->render();
		return $this->stream($name);
	}
}
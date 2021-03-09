<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// https://www.highcharts.com/demo

if (!isset($GLOBALS['tpl_includes']['js'])) {
	$GLOBALS['tpl_includes']['js'] = array();
}

if (!defined('CHART_URL'))
	define('CHART_URL', base_url().'application/libraries/chart/lib/');

function lib_chart($opt = array(), $id = '')
{
	if ($opt) {
		if (!$id) $id = 'chart_'.mt_rand(1000000,9000000);
		return '<div id="'.$id.'"></div>
<script type="text/javascript">
(function() {
	window.addEventListener(\'load\', function() { 
		Highcharts.chart("'.$id.'", '.json_encode($opt).');
	}, false);
})();
</script>';
	}
}

function lib_chart_include($file='')
{
	$file = CHART_URL.$file;
	if (!in_array($file, $GLOBALS['tpl_includes']['js'])) {
		$GLOBALS['tpl_includes']['js'][] = $file;
		echo '<script src="'.$file.'"></script>';
	}
}

lib_chart_include('highcharts.js');
lib_chart_include('modules/exporting.js');
lib_chart_include('modules/export-data.js');
lib_chart_include('modules/accessibility.js');
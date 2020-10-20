<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function lib_bsv()
{
	if (empty($GLOBALS['bsv'])) {
		$GLOBALS['bsv'] = 3;
	}
	$i = intval($GLOBALS['bsv']) - 3;
	$f = func_get_args();
	if (isset($f[$i])) {
		return $f[$i];
	}elseif ($f[0]) {
		return $f[0];
	}else{
		return '';
	}
}
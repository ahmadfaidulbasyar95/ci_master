<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function is_url($text)
{
	$regex		= '/^((?:ht|f)tps?:\/\/)(?:[a-z0-9\-]+\.[a-z0-9\-\.]+|localhost)\/?/is';
	$output = (preg_match($regex, (string)$text)) ? true : false;
	return $output;
}
function is_email($text)
{
	$regex	= '/^[a-z0-9\_\.]+@(?:[a-z0-9\-\.]){1,66}\.(?:[a-z]){2,6}$/is';
	$output = (preg_match($regex, (string)$text)) ? true : false;
	return $output;
}
function is_phone($text)
{
	$regex	= '/^\+?([0-9]+[\s\.\-]?(?:[0-9\s\.\-]+)?){5,}$/is';
	$output = (preg_match($regex, (string)$text)) ? true : false;
	return $output;
}

function redirect($url='')
{
	global $sys, $Bbc;
	if (empty($url))
	{
		if (!empty($_GET['return']))
		{
			$url = $_GET['return'];
		}else
		if (!empty($_GET['_return']))
		{
			$url = $_GET['_return'];
		}else
		if (!empty($_SERVER['HTTP_REFERER']))
		{
			$url = $_SERVER['HTTP_REFERER'];
		}
	}
	if (!empty($url))
	{
		$url = (_ADMIN=='') ? site_url($url) : $url;
	}else{
		$url = _URL._ADMIN;
	}
	header('location:'.$url);
	die($url);
}

function repairExplode($data, $delimeter = ',')
{
	$arr = array();
	if (is_array($data))
	{
		$r = $data;
	}else{
		$r = explode($delimeter, $data);
	}
	foreach($r AS $value)
	{
		if(!empty($value) || $value == '0')
		{
			$arr[] = $value;
		}
	}
	$output = array_unique($arr);
	return $output;
}
function repairImplode($data, $delimeter = ',')
{
	$arr = array();
	foreach((array)$data AS $value)
	{
		if(!empty($value))
		{
			$arr[] = $value;
		}
	}
	$output = implode($delimeter, array_unique($arr));
	if(!empty($output))
	{
		$output = $delimeter.$output.$delimeter;
	}
	return $output;
}

function paramImplode($arr = array())
{
	$output = '';
	if(is_array($arr) and !empty($arr))
	{
		$r = array();
		foreach($arr AS $var => $val){
			if(is_array($val)){
				$val = urlencode(urlencode(paramImplode($val)));
			}else{
				$val = urlencode($val);
			}
			$r[] = $var.'='.$val;
		}
		$output = implode('&', $r);
	}
	return $output;
}
function paramExplode($txt = '')
{
	$output = array();
	if(!empty($txt))
	{
		$arr = explode('&', $txt);
		foreach($arr AS $data){
			$var = substr($data, 0, strpos($data, '='));
			$val = urldecode(substr(strchr($data, '='), 1));
			if(preg_match("/\=/", urldecode(str_replace('=', '', $val)))){
				$val = paramExplode(urldecode($val));
			}
			$output[$var] = $val;
		}
	}
	return $output;
}

function money($price = 0, $is_shorten= false)
{
	$output = $price;
	if ($is_shorten)
	{
	  $x = round($price);
	  $x_number_format = number_format($x);
	  $x_array = explode(',', $x_number_format);
	  $x_parts = array(lang('k'), lang('m'), lang('b'), lang('t'));
	  $x_count_parts = count($x_array) - 1;
	  $output = $x;
	  if (isset($x_array[0]))
	  {
		  $output = $x_array[0];
	  }
	  if (isset($x_array[1]))
	  {
		  $output .= ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
		  $output .= $x_parts[$x_count_parts - 1];
	  }
	}else{
		$output = number_format(floatval($price), 2, '.', ',');
		$output = preg_replace('~0+$~', '',  $output);
		$output = preg_replace('~\.$~', '',  $output);
	}
	return $output;
}

function ids(&$ids, $out_array = false)
{
	$ids     = is_array($ids) ? $ids : array($ids);
	$ids_fix = array();
	foreach ($ids as $value) {
		if (intval($value)) $ids_fix[] = intval($value);
	}
	$ids = $ids_fix;
	if(empty($ids))
	{
		$ids = (!$out_array) ? '' : array();
		return false;
	}
	if(!$out_array) $ids = implode(',', $ids);
	return true;
}

function parseToArray($r_select, $r_all)
{
	$output = array();
	if(is_array($r_select))
		foreach($r_select AS $id => $dt)
			if($r_all[$id]!='')
				$output[] = $r_all[$id];
	return $output;
}
function fixArray($val = array())
{
	if(!empty($val)){
		$output = is_array($val) ? array_unique($val) : array($val);
	}else{
		$output = array(0);
	}
	return $output;
}
function fixValue($value = 0)
{
	if(!empty($value)){
		$r = repairExplode($value);
		$output = implode(',', $r);
	}else{
		$output = 0;
	}
	return $output;
}

function addslashes_r($vars)
{
	$vars = is_array($vars) ? array_map(__FUNCTION__, $vars) : addslashes($vars);
	return $vars;
}
function stripslashes_r($vars)
{
	$vars = is_array($vars) ? array_map(__FUNCTION__, $vars) : stripslashes($vars);
	return $vars;
}
function urlencode_r($vars)
{
	$vars = is_array($vars) ? array_map(__FUNCTION__, $vars) : urlencode($vars);
	return $vars;
}
function urldecode_r($vars)
{
	$vars = is_array($vars) ? array_map(__FUNCTION__, $vars) : urldecode($vars);
	return $vars;
}
function htmlentities_r($vars)
{
	$vars = is_array($vars) ? array_map(__FUNCTION__, $vars) : htmlentities($vars);
	return $vars;
}
function unhtmlentities($cadena)
{
	return html_entity_decode($cadena);
  $cadena = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $cadena);
  $cadena = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $cadena);
  $trans_tbl = get_html_translation_table(HTML_ENTITIES);
  $trans_tbl = array_flip($trans_tbl);
  return strtr($cadena, $trans_tbl);
}
function unhtmlentities_r($vars)
{
	$vars = is_array($vars) ? array_map(__FUNCTION__, $vars) : unhtmlentities($vars);
	return $vars;
}

function justify_text($txt, $txt2, $spaces, $add = ' ')
{
  $i = strlen($txt);
  $j = strlen($txt2);
  $out = $txt;
  $k = $spaces - $i - $j;
  if($k > 0)
  {
    $out .= str_repeat($add, $k);
  }else{

    $x = 4;
    $y = $spaces - $j - $x;
    $out = substr($txt, 0, $y);
    $out .= str_repeat('.', $x - 1).$add;
  }
  $out .= $txt2;
  return $out;
}

function config_decode($string)
{
	$out = urldecode_r(json_decode($string, 1));
	if (empty($out))
	{
		$out = array();
	}
	return $out;
}
function config_encode($array)
{
	return json_encode(urlencode_r($array));
}

function setCache($name = '', $value = '')
{
	if ($name) {
		file_put_contents(APPPATH.'cache/'.$name, (is_array($value)) ? config_encode($value) : $value );
		file_put_contents(APPPATH.'cache/'.$name.'_is_array', (is_array($value)) ? 1 : 0 );
	}
}
function getCache($name = '')
{
	if (is_file(APPPATH.'cache/'.$name)) {
		$is_array = 0;
		if (is_file(APPPATH.'cache/'.$name.'_is_array')) {
			$is_array = file_get_contents(APPPATH.'cache/'.$name.'_is_array');
		}
		$data = file_get_contents(APPPATH.'cache/'.$name);
		return (intval($is_array)) ? config_decode($data) : $data ;
	}
	return '';
}
function delCache($name = '')
{
	if (is_file(APPPATH.'cache/'.$name)) {
		unlink(APPPATH.'cache/'.$name);
	}
	if (is_file(APPPATH.'cache/'.$name.'_is_array')) {
		unlink(APPPATH.'cache/'.$name.'_is_array');
	}
}
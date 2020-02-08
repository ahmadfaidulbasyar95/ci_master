<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function lib_output_json($array)
{
	$output = '{}';
	if (!empty($array))
	{
		if (is_object($array))
		{
			$array = (array)$array;
		}
		if (!is_array($array))
		{
			$output = $array;
		}else{
			if (defined('JSON_PRETTY_PRINT'))
			{
				$output = json_encode($array, JSON_PRETTY_PRINT);
			}else{
				$output = json_encode($array);
			}
		}
	}
	header('content-type: application/json; charset: UTF-8');
	header('cache-control: must-revalidate');
	header('expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
	echo $output;
	exit();
}
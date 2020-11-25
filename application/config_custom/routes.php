<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['([a-z0-9-]+).html'] = function($value='') {
	$value = file_get_contents($this->config->config['base_url'].'_Pea/menu_get?v='.urlencode($value));
	$value = @(array)json_decode($value, 1);
	foreach ($value['get'] as $key1 => $value1) {
		$_GET[$key1] = $value1;
	}
	return @$value['task'];
};
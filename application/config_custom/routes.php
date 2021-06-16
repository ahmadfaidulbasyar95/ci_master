<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['([a-z0-9-]+).html'] = function($value='') {
	$value = file_get_contents($this->config->config['base_url'].'_T/menu_get?v='.urlencode($value));
	$value = @(array)json_decode($value, 1);
	foreach ($value['get'] as $key1 => $value1) {
		if (!isset($_GET[$key1])) {
			$_GET[$key1] = $value1;
		}
	}
	return @$value['task'];
};

$c_dashboard = FCPATH.'files/cache/config/dashboard.cfg';
if (is_file($c_dashboard)) {
	$c_dashboard = json_decode(file_get_contents($c_dashboard), 1);
	if ($c_dashboard) {
		$route[$c_dashboard['login_uri']] = 'admin/user/login';
	}
}

if ($GLOBALS['CFG']->config['base_url'] == $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']) {
	$c_site = FCPATH.'files/cache/config/site.cfg';
	if (is_file($c_site)) {
		$c_site = json_decode(file_get_contents($c_site), 1);
		if ($c_site) {
			$parse_url = parse_url($c_site['home_uri']);
			parse_str(@$parse_url['query'], $parse_url['query']);
			foreach ($parse_url['query'] as $key => $value) {
				if (!isset($_GET[$key])) {
					$_GET[$key] = $value;
				}
			}
			$route['default_controller'] = preg_replace('~\/$~','',$parse_url['path']);
		}
	}
}
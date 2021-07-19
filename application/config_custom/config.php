<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$c_site = FCPATH.'files/cache/config/site.cfg';
if (is_file($c_site)) {
	$c_site = json_decode(file_get_contents($c_site), 1);
	if ($c_site) {
		$config['base_url'] = $_SERVER['REQUEST_SCHEME'].'://'.$c_site['domain'].str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
	}
}
if (!empty($config['encryption_key'])) {
	define('_SALT', $config['encryption_key']);
}
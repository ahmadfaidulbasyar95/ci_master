<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!empty($_GET['v'])) {
	$this->load->model('_db_model');
	$url       = $this->_db_model->getOne('SELECT `url` FROM `menu` WHERE `uri`="'.addslashes($_GET['v']).'"');
	$parse_url = parse_url($url);
	include_once __DIR__.'/../../output.php';
	parse_str(@$parse_url['query'], $parse_url['query']);
	lib_output_json(array(
		'task' => $parse_url['path'],
		'get'  => $parse_url['query']
	));
}
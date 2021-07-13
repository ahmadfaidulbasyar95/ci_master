<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (isset($_GET['text'])) {
	$path = $this->_root.'files/cache/qr/';
	$name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $_GET['text']).'.png';
	if (!is_file($path.$name)) {
		if (!file_exists($path)) {
			if (mkdir($path, 0777)) {
				chmod($path, 0777);
			}
		}
		$opt = array(
			'text'   => 'Text',
			'level'  => 0, // 0,1,2,3
			'size'   => 3,
			'margin' => 2,
		);
		foreach ($_GET as $key => $value) {
			if (isset($opt[$key])) $opt[$key] = $value;
		}
		include_once __DIR__.'/../../qr/lib/phpqrcode.php';
		QRcode::png($opt['text'], $path.$name, $opt['level'], $opt['size'], $opt['margin']);
	}
	echo file_get_contents($path.$name);
}
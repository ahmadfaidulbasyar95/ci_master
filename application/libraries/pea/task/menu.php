<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!empty($_POST['v'])) {
	$this->load->model('_db_model');
	$id = @intval($_POST['id']);
	$v  = $_POST['v'];
	$v  = preg_replace('~[^a-z0-9]~', '-', strtolower($v));
	$i  = '';
	while ($this->_db_model->getOne('SELECT 1 FROM `menu` WHERE `uri`="'.addslashes($v.$i).'" AND `id`!='.$id) == 1) {
		if (is_numeric($i)) {
			$i++;
		}else{
			$i = 0;
		}
	}
	$v .= $i;
	echo $v;
}
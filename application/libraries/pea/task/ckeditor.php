<?php
defined('BASEPATH') OR exit('No direct script access allowed');

ob_start();
$this->load->model('_tpl_model');
$this->_tpl_model->setLayout('blank');
$this->_tpl_model->show();
ob_clean();
$this->_tpl_model->lib('output');
lib_output_json($GLOBALS['tpl_includes']);
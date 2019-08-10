<?php  if (!defined('CIM_SYSTEM')) exit('No direct script access allowed');

define('CIM_ALIAS', 'sys');
define('CIM_URI', '/mygit/ci_master/');
define('CIM_CONTROLLER', 'admin');

define('CIM_URL', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].CIM_URI);
define('CIM_ROOT', FCPATH);
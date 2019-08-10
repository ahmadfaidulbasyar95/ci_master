<?php 
define('CIM_SYSTEM', '1');

include_once dirname(__FILE__).'/config.php';
include_once dirname(__FILE__).'/includes/function.php';
include_once dirname(__FILE__).'/includes/index.php';

$GLOBALS[CIM_ALIAS] = new CIM_SYSTEM();
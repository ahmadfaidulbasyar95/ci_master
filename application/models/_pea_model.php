<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
Check the .htaccess file in /application/.htaccess
it must allow accessing image, css, js file
For the example : 
----------------------------------------------------------------------------------------
# deny *everything*
<FilesMatch ".*">
  Order Allow,Deny
  Deny from all
</FilesMatch>

# but now allow just *certain* necessary files:
<FilesMatch ".*\.(js|JS|css|CSS|jpg|JPG|gif|GIF|png|PNG|swf|SWF|xsl|XSL)$">
  Order Allow,Deny
  Allow from all
</FilesMatch>
----------------------------------------------------------------------------------------
*/
class _pea_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('_db_model');

		$GLOBALS['pea_includes'] = array(
			'js'  => array(),
			'css' => array(),
		);
		$GLOBALS['pea_includes_load'] = $GLOBALS['pea_includes'];

		include_once APPPATH.'libraries/pea/pea.php';
	}

	public function newForm($table)
	{
		return new lib_pea($table, $this->_db_model);
	}
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function lib_qr($opt = array())
{
	if (!is_array($opt)) $opt = array('text' => $opt);
	return base_url().'_T/qr?'.http_build_query($opt);
}
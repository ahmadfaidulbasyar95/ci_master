<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function lib_qr($opt = array())
{
	
	return base_url().'_T/qr?'.http_build_query($opt);
}
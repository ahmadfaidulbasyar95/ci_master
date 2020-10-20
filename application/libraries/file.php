<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function lib_file_read($file = '', $method = 'r')
{
	if ( empty($file) || !file_exists($file))
	{
		return FALSE;
	}
	if (function_exists('file_get_contents'))
	{
		return file_get_contents($file);
	}
	if ( ! $fp = @fopen($file, $method))
	{
		return FALSE;
	}
	flock($fp, LOCK_SH);
	$data = '';
	if (filesize($file) > 0)
	{
		$data =& fread($fp, filesize($file));
	}
	flock($fp, LOCK_UN);
	fclose($fp);
	return $data;
}

function lib_file_write($path, $data='', $mode = 'w+')
{
	if (empty($path))
	{
		return FALSE;
	}
	if(!file_exists(dirname($path)))
	{
		include_once __DIR__.'/path.php';
		lib_path_create(dirname($path).'/');
	}
	if ( ! $fp = @fopen($path, $mode))
	{
		return FALSE;
	}
	flock($fp, LOCK_EX);
	fwrite($fp, $data);
	flock($fp, LOCK_UN);
	fclose($fp);
	@chmod($path, 0777);
	return TRUE;
}

function lib_file_octal_permissions($perms)
{
	if(file_exists($perms)) $perms = fileperms($perms);
	return substr(sprintf('%o', $perms), -3);
}

function lib_file_names($source_dir, $include_path = FALSE, $_recursion = FALSE)
{
	static $_filedata = array();

	if ($fp = @opendir($source_dir))
	{
		// reset the array and make sure $source_dir has a trailing slash on the initial call
		if ($_recursion === FALSE)
		{
			$_filedata = array();
			$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		}

		while (FALSE !== ($file = readdir($fp)))
		{
			if (@is_dir($source_dir.$file) && strncmp($file, '.', 1) !== 0)
			{
				 lib_file_names($source_dir.$file.DIRECTORY_SEPARATOR, $include_path, TRUE);
			}
			elseif (strncmp($file, '.', 1) !== 0)
			{

				$_filedata[] = ($include_path == TRUE) ? $source_dir.$file : $file;
			}
		}
		return $_filedata;
	}
	else
	{
		return FALSE;
	}
}

function lib_file_size($filepath, $decimals = 2)
{
	$output = '';
	if (is_file($filepath))
	{
		$bytes = filesize($filepath);
	  $sz     = 'BKMGTP';
	  $factor = floor((strlen($bytes) - 1) / 3);
	  $output = sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	  $output.= @$sz[$factor] != 'B' ? 'b' : '';
	}
	return $output;
}
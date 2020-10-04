<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function lib_path_list($path, $order = 'asc')
{
	$output = array();
	if ($dir = @opendir($path)) {
		while (($data = readdir($dir)) !== false)
		{
			if($data != '.' and $data != '..')
			{
				$output[] = $data;
			}
		}
		closedir($dir);
	}
	if(strtolower($order) == 'desc') rsort($output);
	else sort($output);
	reset($output);
	return $output;
}

function lib_path_list_r($path, $top_level_only = FALSE)
{
	if ($fp = @opendir($path))
	{
		$path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		$filedata = array();
		while (FALSE !== ($file = readdir($fp)))
		{
			if (strncmp($file, '.', 1) == 0)
			{
				continue;
			}
			if ($top_level_only == FALSE && @is_dir($path.$file))
			{
				$temp_array = array();
				$temp_array = call_user_func(__FUNCTION__, $path.$file.DIRECTORY_SEPARATOR);
				$filedata[$file] = $temp_array;
			}
			else
			{
				$filedata[] = $file;
			}
		}
		closedir($fp);
		return $filedata;
	}
	return false;
}

function lib_path_delete($path)
{
	if($path == FCPATH) return false;
	elseif(!preg_match('~^'.FCPATH.'~', $path)) return false;
	if (file_exists($path))
	{
		@chmod($path,0777);
		if (is_dir($path))
		{
			$handle = opendir($path);
			while(false !== ($filename = readdir($handle)))
			{
				if ($filename != '.' && $filename != '..')
				{
					call_user_func(__FUNCTION__, $path.'/'.$filename);
				}
			}
			closedir($handle);
			@rmdir($path);
		} else {
			@unlink($path);
		}
	}
}

function lib_path_create($path, $chmod = 0777)
{
	if(!empty($path))
	{
		if(file_exists($path)) $output = true;
		else {
			$path = preg_replace('~^'.addslashes(FCPATH).'~s', '', $path);
			$tmp_dir = FCPATH;
			$r = explode('/', $path);
			foreach($r AS $dir)
			{
				$tmp_dir .= $dir.'/';
				if(!file_exists($tmp_dir))
				{
					if(mkdir($tmp_dir, $chmod))
					{
						chmod($tmp_dir, $chmod);
					}
				}
			}
			$output = (file_exists($tmp_dir)) ? true : false;
		}
	}else{
		$output = false;
	}
	return $output;
}
<?php  if (!defined('CIM_SYSTEM')) exit('No direct script access allowed');

/**
 * CI Master System
 */
class CIM_SYSTEM
{
	
	function __construct()
	{
		
	}

	public function init($module = '', $task = array())
	{
		if ($module == 'index') {
			set_include_path(dirname(__FILE__).'/modules/cpanel');
			include 'index.php';
		}else
		if (file_exists(dirname(__FILE__).'/modules/'.$module.'/_switch.php')) {
			if (!is_array($task)) $task = array($task);
			if (!$task) $task[]         = 'main';
			$mod                        = array(
				'name' => $module,
				'task' => $task,
				'url'  => CIM_URL.CIM_CONTROLLER.'/'.$module.'/',
			);
			unset($module);
			unset($task);
			set_include_path(dirname(__FILE__).'/modules/'.$mod['name']);
			include '_switch.php';
		}else{
			die('Invalid Module '.$module);
		}
	}

	public function output_json($array)
	{
		$output = '{}';
		if (!empty($array))
		{
			if (is_object($array))
			{
				$array = (array)$array;
			}
			if (!is_array($array))
			{
				$output = $array;
			}else{
				if (defined('JSON_PRETTY_PRINT'))
				{
					$output = json_encode($array, JSON_PRETTY_PRINT);
				}else{
					$output = json_encode($array);
				}
			}
		}
		header('content-type: application/json; charset: UTF-8');
		header('cache-control: must-revalidate');
		header('expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
		echo $output;
		exit();
	}

	public function curl_parse_param_get($param_get=array(), $param_key='', $is_top=1)
	{
		$output = array();
		if (is_array($param_get)) {
			foreach ($param_get as $key => $value) {
				if ($param_key) {
					$key = $param_key.'['.$key.']';
				}
				foreach ($this->curl_parse_param_get($value,$key,0) as $value1) {
					$output[] = $value1;
				}
			}
		}else{
			$output[] = $param_key.'='.urlencode($param_get);
		}
		if ($is_top) {
			return ($output) ? implode('&', $output) : '' ;
		}else{
			return $output;
		}
	}

	public function curl($url, $param_get=array(), $param_post=array(), $option=array(), $is_debug = false)
	{
		if ($is_debug) {
			$param_debug = array(
				'_GET'  => $param_get,
				'_POST' => $param_post,
			);
		}
		if ($param_get and is_array($param_get)) {
			$param_get = $this->curl_parse_param_get($param_get);
			$url .= '?'.$param_get;
		}
		if(!preg_match('~^(?:ht|f)tps?://~', $url) && file_exists($url))
		{
			return file_get_contents($url);
		}else{
			if(!preg_match('~^(?:ht|f)tps?://~', $url)) {
				$url = 'http://'.$url;
			}
		}
		$temp = '/tmp/curl';
		if(is_numeric($param_post))
		{
			$text			= unserialize(curl($temp.'_'.md5($url)));
			if(!empty($text[0]) && $text[0] > time())
			{
				return @$text[1];
			}
			$presists	= intval($param_post);
			$param_post		= array();
		}else $presists	= 0;
		$default = array(
			'CURLOPT_REFERER'    => !empty($_SESSION['CURLOPT_REFERER']) ? $_SESSION['CURLOPT_REFERER'] : $url,
			'CURLOPT_POST'       => empty($param_post) ? 0 : 1,
			'CURLOPT_POSTFIELDS' => $param_post,
			'CURLOPT_USERAGENT'  => @$_SERVER['HTTP_USER_AGENT'],
			'CURLOPT_HEADER'     => 1,
			'CURLOPT_HTTPHEADER' => array(
				'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'Accept-Language: en-US,en;q=0.5',
				'Accept-Encoding: gzip, deflate',
				'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
				'Keep-Alive: 300',
				'Connection: keep-alive',
				'Content-Type: application/x-www-form-urlencoded'),
			'CURLOPT_FOLLOWLOCATION' => 0,
			'CURLOPT_RETURNTRANSFER' => 1,
			'CURLOPT_COOKIEFILE'     => $temp,
			'CURLOPT_COOKIEJAR'      => $temp
			);
		foreach ($option as $key => $value) {
			if (empty($value) && $value!='0') {
				unset($option[$key]);
			}
		}
		$data = array_merge($default, $option);
		$data['CURLOPT_POST'] = empty($data['CURLOPT_POSTFIELDS']) ? 0 : 1;

		if($data['CURLOPT_POST']) {
			$data['CURLOPT_POSTFIELDS'] = http_build_query($data['CURLOPT_POSTFIELDS']);
		}else unset($data['CURLOPT_POSTFIELDS']);

		// $data['CURLOPT_HTTPHEADER'] = array_map('urlencode', $data['CURLOPT_HTTPHEADER']);
		$data['CURLOPT_HTTPHEADER'] = $data['CURLOPT_HTTPHEADER'];

		if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
		}else unset($data['CURLOPT_FOLLOWLOCATION']);

		if(strtolower(substr($url, 0, 5)) == 'https') {
			$data['CURLOPT_FOLLOWLOCATION'] = 0;
			$data['CURLOPT_SSL_VERIFYHOST'] = 0;
		}

		$init = curl_init( $url );
		foreach ($data as $key => $value) {
			curl_setopt($init, constant($key), $value);
		}
		$out  = curl_exec($init);
		$info = curl_getinfo($init);
		if (!empty($info['header_size'])) {
			$header = substr($out, 0, $info['header_size']);
			$output = substr($out, $info['header_size']);
		}else{
			$header = '';
			$output = $out;
		}
		if (!empty($info['redirect_url'])) {
			$_SESSION['CURLOPT_REFERER'] = $info['redirect_url'];
		}else{
			$_SESSION['CURLOPT_REFERER'] = $url;
		}
		if ( $is_debug )
		{
			$debug = array(
				'url'    => $url,
				'params' => $param_debug
			);
			if (!empty($param_debug['_GET'])) {
				$debug['params']['encoded']['_GET'] = $param_get;
			}else{
				unset($debug['params']['_GET']);
			}
			if(!empty($data['CURLOPT_POSTFIELDS']))
			{
				$debug['params']['encoded']['_POST'] = htmlentities($data['CURLOPT_POSTFIELDS']);
			}else{
				unset($debug['params']['_POST']);
			}
			$a = curl_errno( $init );
			if(!empty($a))
			{
				$debug['ErrNum'] = $a;
			}
			$a = curl_error( $init );
			if(!empty($a))
			{
				$debug['ErrMsg'] = $a;
			}
			echo 'Request :';
			if(empty($debug))
			{
				echo $output;
			}else{
				$debug['info']   = $info;
				$debug['header'] = $header;
				$debug['output'] = $output;
				if (!empty($_POST['is_plain'])) {
					print_r($debug);
				}else{
					echo '<pre>'.print_r($debug, 1).'</pre>';
				}
			}
		}
		curl_close($init);
		if($presists > 0 && !empty($output))
		{
			if ( $fp = @fopen($temp.'_'.md5($url), 'w+'))
			{
				flock($fp, LOCK_EX);
				fwrite($fp, serialize(array(strtotime('+'.$presists.' SECOND'), $output)));
				flock($fp, LOCK_UN);
				fclose($fp);
			}
		}
		if (strpos($header, 'Content-Encoding: gzip')) {
			$output = gzinflate(substr($output, 10));
		}
		return $output;
	}

}
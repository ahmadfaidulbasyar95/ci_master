<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function lib_table($data, $header = array(), $title='')
{
	$output = '';
	if (!empty($data))
	{
		$tHead = '';
		$tBody = '';
		if (!empty($header) && !is_array($header))
		{
			if (empty($title))
			{
				$title = $header;
			}
			$header = array();
		}
		if (!empty($header))
		{
			$tHead = '<thead><tr><th>'.implode('</th><th>', $header).'</th></tr></thead>';
			$rows  = array();
			foreach ($data as $row)
			{
				$rows[] = '<td>'.implode('</td><td>', $row).'</td>';
			}
			$tBody = '<tbody><tr>'.implode('</tr><tr>', $rows).'</tr></tbody>';
		}else{
			foreach ((array)$data as $key => $value)
			{
				if (is_array($value))
				{
					$value = call_user_func(__FUNCTION__, $value);
				}
				$tBody .= '<tr><th>'.$key.'</th><td>'.$value.'</td></tr>';
			}
		}
		$output = '<table class="table table-striped table-bordered table-hover">'.$tHead.$tBody.'</table>';
		if (!empty($title))
		{
			$output = '
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">'.$title.'</h3>
					</div>
					<div class="panel-body">
						'.$output.'
					</div>
				</div>';
		}
	}
	return $output;
}
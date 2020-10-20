<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__.'/bsv.php';
function lib_pagination($found, $show, $curr=0, $var='', $link='', $maxpage=12, $interval=0, $config_view = array())
{
	$output    = '';
	$totalpage = ceil($found/$show);
	if($found > 1) {
		$config_view_def = array(
			'prev_msg'        => lib_bsv('<h5>Result {from} to {to} from total {total}</h5>', '<h6>Result {from} to {to} from total {total}</h6>'),
			'full_tag_open'   => lib_bsv('<ul class="pagination">', '<nav aria-label="..."><ul class="pagination">'),
			'first_tag_open'  => lib_bsv('<li>', '<li class="page-item">'),
			'first_link'      => '&laquo;&laquo;',
			'first_tag_close' => '</li>',
			'prev_tag_open'   => lib_bsv('<li>', '<li class="page-item">'),
			'prev_link'       => '&laquo;',
			'prev_tag_close'  => '</li>',
			'num_tag_open'    => lib_bsv('<li>', '<li class="page-item">'),
			'num_tag_close'   => '</li>',
			'num_link'        => lib_bsv('<a href="{link}">{title}</a>', '<a class="page-link" href="{link}">{title}</a>'),
			'cur_tag_open'    => lib_bsv('<li class="active">', '<li class="page-item active" aria-current="page">'),
			'cur_tag_close'   => '</li>',
			'next_tag_open'   => lib_bsv('<li>', '<li class="page-item">'),
			'next_link'       => '&raquo;',
			'next_tag_close'  => '</li>',
			'last_tag_open'   => lib_bsv('<li>', '<li class="page-item">'),
			'last_link'       => '&raquo;&raquo;',
			'last_tag_close'  => '</li>',
			'full_tag_close'  => lib_bsv('</ul>', '</ul></nav>'),
			'go_tag_open'     => lib_bsv('<ul class="pagination"><li>', '<nav aria-label="..."><ul class="pagination"><li>'),
			'go_question'     => 'Go to page ? of {totalpage}',
			'go_link'         => 'Go to',
			'go_tag_close'    => '</li></ul>',
		);
		foreach ($config_view as $key => $value) {
			if (isset($config_view_def[$key])) $config_view_def[$key] = $value;
		}

		$data_to = ($curr*$show)+$show;
		$output .= str_replace(['{from}','{to}','{total}'], [number_format(($curr*$show)+1), number_format(($found > $data_to) ? $data_to : $found), number_format($found)], $config_view_def['prev_msg']);
		if ($totalpage > 1) 
		{
			if(intval($interval)==0) $interval = intval($maxpage / 2);
			$link = ($link) ? $link : $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			if($var) {
				$link = preg_replace('~'.$var.'=[0-9]+&?~', '', $link);
				$link .= (preg_match('/\?/', $link)) ? (preg_match('~[\?|&]$~', $link)) ? '' : '&' : '?';
				$link .= $var.'=';
			}
			
			$output .= $config_view_def['full_tag_open'];
			if($curr > 0) {
				$output .= $config_view_def['first_tag_open'].str_replace(['{link}','{title}'], [preg_replace('~[?|&]'.$var.'=$~', '', $link), $config_view_def['first_link']], $config_view_def['num_link']).$config_view_def['first_tag_close'];
				$output .= $config_view_def['prev_tag_open'].str_replace(['{link}','{title}'], [$link.($curr - 1), $config_view_def['prev_link']], $config_view_def['num_link']).$config_view_def['prev_tag_close'];
			}
			if(($interval+$curr) >= $maxpage) {
				$iend   = ($curr + $interval + 1);
				$istart = $iend - $maxpage;
			}else{
				$istart = 0;
				$iend   = $istart + $maxpage;
			}
			if($iend > $totalpage) $iend = $totalpage;
			for ($i = $istart; $i < $iend; $i++) {
				$j       = $i + 1;
				$href    = $i ? $link.$i : preg_replace('~[?|&]'.$var.'=$~', '', $link);
				$output .= ($curr==$i) ? $config_view_def['cur_tag_open'] : $config_view_def['num_tag_open'];
				$output .= str_replace(['{link}','{title}'], [$href, number_format($j)], $config_view_def['num_link']);
				$output .= ($curr==$i) ? $config_view_def['cur_tag_close'] : $config_view_def['num_tag_close'];
			}
			if(($curr + 1) < $totalpage) {
				$output .= $config_view_def['next_tag_open'].str_replace(['{link}','{title}'], [$link.($curr + 1), $config_view_def['next_link']], $config_view_def['num_link']).$config_view_def['next_tag_close'];
				$output .= $config_view_def['last_tag_open'].str_replace(['{link}','{title}'], [$link.($totalpage - 1), $config_view_def['last_link']], $config_view_def['num_link']).$config_view_def['last_tag_close'];
			}
			$output .= $config_view_def['full_tag_close'];

			if($totalpage > $maxpage) {
				$output .= $config_view_def['go_tag_open'];
				$output .= '<a href="#" onclick="var page = prompt(\''.str_replace('{totalpage}', $totalpage, $config_view_def['go_question']).'\'); if (parseInt(page) > 0) { if (parseInt(page) <= '.$totalpage.') { page = page - 1; window.location.href=\''.$link.'\'+page;} } return false;">'.$config_view_def['go_link'].'</a>';
				$output .= $config_view_def['go_tag_close'];
			}
		}
	}
	return $output;
}
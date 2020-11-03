<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function lib_tabs($data, $use_cookie = 1, $name='', $maxwidth = false, $r_iframe = array())
{
	$output = '';
	if (!empty($data) && is_array($data))
	{
		if (count($data) < 2)
		{
			$output = current($data);
		}else{
			if (empty($name))
			{
				if (empty($GLOBALS['tabs']))
				{
					$GLOBALS['tabs'] = 0;
				}
				$name = str_replace(['.php',DIRECTORY_SEPARATOR],'', @debug_backtrace()[0]['file']);
				$name = substr($name, -15);
				$name = 'tabs'.$name.($GLOBALS['tabs']++);
			}
			if (!isset($GLOBALS['tabs_is_url']))
			{
				$GLOBALS['tabs_is_url'] = false;
				$load_script            = true;
			}else $load_script = false;
			$r_pane = $r_page = array();$i = 0;
			foreach ($data as $title => $content)
			{
				$div    = $name.'_'.$i++;
				$active = $i == 1? array(' class="active"',' active') : array('','');
				if(filter_var($content, FILTER_VALIDATE_URL))
				{
					if(in_array($content, $r_iframe) || $r_iframe == 'all')
					{
						$r_pane[] = '<li'.$active[0].'><a href="#'.$div.'" rel="'.$content.'" data-toggle="tab">'.$title.'</a></li>';
						$content  = '<iframe src="'.$content.'" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0" style="width:100%; height:auto; min-height: 100px"></iframe>';
					}else{
						$r_pane[]               = '<li'.$active[0].'><a href="#'.$div.'" rel="'.$content.'" data-toggle="url">'.$title.'</a></li>';
						$GLOBALS['tabs_is_url'] = true;
						$content                = '<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">Please wait...</span></div></div>';
					}
				}else{
					$r_pane[] = '<li'.$active[0].'><a href="#'.$div.'" data-toggle="tab">'.$title.'</a></li>';
				}
				$r_page[] = '<div id="'.$div.'" class="tab-pane'.$active[1].'">'.$content.'</div>';
			}
			$cls = $maxwidth ? ' nav-justified' : '';
			$output = '<ul class="nav nav-tabs'.$cls.'" cookie="'.$use_cookie.'" >'
							.	implode("\n", $r_pane).'</ul>'
							.	'<div class="tab-content">'.implode("\n", $r_page).'</div>';
		}
	}
	return $output;
}
?>
<script type="text/javascript">
(function() {
	window.addEventListener('load', function() { 
		var BS3_cookie = function (e, t, a) {
			e = "bs3" + e;
			if (typeof t == "undefined") {
				t = "";
			}
			if (typeof a == "undefined") {
				var a = new RegExp(e + t + "=([^;]+)(.*?)$", "i");
				var r = document.cookie.match(a);
				if (r) {
					return r[1];
				}
			} else {
				document.cookie = e + t + "=" + a + ";path=" + document.location.pathname + "; SameSite=None; Secure";
			}
			return "";
		};
		$(".nav-tabs").find("a[data-toggle=url]").click(function () {
			var e = $(this).attr("data-toggle");
			if (e == "url") {
				$(this).attr("data-toggle", "tab");
				$($(this).attr("href")).load($(this).attr("rel"));
			}
			$(this).tab("show");
			return false;
		});
		$(".nav-tabs[cookie=1]").each(function (e) {
			$("a", $(this)).click(function () {
				BS3_cookie("tab", e, $(this).attr("href"));
			});
			var t = BS3_cookie("tab", e);
			if (typeof t == "string" && t != "") {
				$("a[href=\"" + t + "\"]", $(this)).trigger("click");
			}
		});
	}, false);
})();
</script>
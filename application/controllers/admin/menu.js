(function() {
	window.addEventListener('load', function() { 
		$('[rel="menu_url"]').on('focusout', function(event) {
			var e = $(this);
			var v = e.val();
			if (v) {
				if (v.indexOf(_URL) == -1) {
					var t = 1;
				}else{
					var t = 0;
					v = v.replace(_URL, '');
					e.val(v);
				}
				$('[rel="menu_url_type"]').val(t);
			}
		});
	}, false);
})();
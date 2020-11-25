(function() {
	window.addEventListener('load', function() { 
		$('[rel="menu_url"]').on('focusout', function(event) {
			var e = $(this);
			var v = e.val();
			if (v) {
				if (v.indexOf(_URL) == -1 && v.indexOf('://') != -1) {
					var t = 1;
				}else{
					var t = 0;
					v = v.replace(_URL, '');
					e.val(v);
					e.parents('form').find('[rel="menu_title"]').trigger('keyup');
				}
				e.parents('form').find('[rel="menu_url_type"]').val(t);
			}
		});
		$('[rel="menu_title"]').on('keyup', function(event) {
			var e = $(this);
			var v = e.val();
			var u = e.parents('form').find('[rel="menu_uri"]');
			if (u.length) {
				if (v) {
					$.ajax({
						url: _URL+'_Pea/menu',
						type: 'POST',
						dataType: 'html',
						data: {v: v, id: u.data('id')},
					})
					.done(function(out) {
						u.val(out);
					});
				}else{
					u.val('');
				}
			}
		});
		$('[rel="menu_uri"]').on('focusout', function(event) {
			var e = $(this);
			var v = e.val();
			if (v) {
				$.ajax({
					url: _URL+'_Pea/menu',
					type: 'POST',
					dataType: 'html',
					data: {v: v, id: e.data('id')},
				})
				.done(function(out) {
					e.val(out);
				});
			}
		});
	}, false);
})();
(function() {
	window.addEventListener('load', function() {
		$('body').on('keyup change', '.uniq_value', delay(function(event) {
			var el = $(this);
			var tk = el.data('uniq_token');
			if (tk) {
				var v = el.val();
				var p = el.parent('.form-group');
				var e = p.find('.uniq_error');
				var s = p.find('.uniq_success');
				if (v) {
					var t = el.data('uniq_type');
					switch (t) {
						case 'phone':
						case 'tel':
							var v_ = v.replace(/^0/, '62', v);
							if (v != v_) {
								v = v_;
								el.val(v);
							}
							break;
					}
					$.ajax({
						url: _URL+'_T/getdata',
						type: 'POST',
						dataType: 'json',
						data: {token: tk, v: v},
					})
					.done(function(out) {
						if (out[0] != undefined) {
							e.show();
							s.hide();
						}else{
							e.hide();
							s.show();
						}
					})
					.fail(function() {
						alert("Something Wrong. Please try again later or reload this page !");
					});
				}else{
					e.hide();
					s.hide();
				}
			}
		}, 1000));
	}, false);
})();
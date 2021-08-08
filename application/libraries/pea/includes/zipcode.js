(function() {
	window.addEventListener('load', function() { 
		$('.fm_zipcode').each(function(index, el) {
			var elem = $(this);
			if (!elem.data('fm_zipcode_load')) {
				elem.data('fm_zipcode_load', 1);
				var token   = elem.data('token');
				var val_def = elem.val();
				elem.on('keyup', function(event) {
					val_def = elem.val();
				});
				$('[name="'+elem.data('dependent')+'"]').on('change', function(event) {
					var v = $(this).val();
					if (v) {
						$.ajax({
							url: _URL+'_T/getdata',
							type: 'POST',
							dataType: 'json',
							data: {token: token, v: v},
						})
						.done(function(out) {
							var x = (out[0] != undefined) ? out[0]['value'] : 0;
							if (x) {
								elem.val(x).attr('readonly', 'readonly');
							}else{
								elem.val(val_def).removeAttr('readonly');
							}
						})
						.fail(function() {
							alert("Something Wrong. Please try again later or reload this page !");
						});
					}else{
						elem.val(val_def).removeAttr('readonly');
					}
				});
			}
		});
	}, false);
})();
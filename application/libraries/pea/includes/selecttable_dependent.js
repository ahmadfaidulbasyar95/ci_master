(function() {
	window.addEventListener('load', function() { 
		$('.selecttable_dependent').each(function(index, el) {
			var elem = $(this);
			if (!elem.data('selecttable_dependent_load')) {
				elem.data('selecttable_dependent_load', 1);
				var value      = elem.data('value');
				var token      = elem.data('token');
				var dependent  = elem.data('dependent');
				var option_def = elem.html();
				elem.html(option_def);
				$('[name="'+dependent+'"]').on('change', function(event) {
					var v = $(this).val(); 
					if (v) {
						$.ajax({
							url: _URL+'_Pea/getdata',
							type: 'POST',
							dataType: 'json',
							data: {token: token, v: v},
						})
						.done(function(out) {
							var option = option_def;
							$.each(out, function(index, val) {
								if (value == val.value) {
									val.selected = ' selected';
								}else{
									val.selected = '';
								}
								option += '<option value="'+val.value+'"'+val.selected+'>'+val.key+'</option>';
							});
							elem.html(option).trigger('change');
						});
					}else{
						elem.html(option_def).trigger('change');
					}
				}).trigger('change');
			}
		});
	}, false);
})();
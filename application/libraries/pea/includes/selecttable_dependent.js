(function() {
	window.addEventListener('load', function() { 
		if (window.getOptionNested  == undefined) {
			window.getOptionNested = function(option = [], nested = 0, prefix = '') {
				var option_ = [];
				var prefix_ = (prefix) ? prefix+' ' : '';
				$.each(option, function(key, value) {
					if (value.nested == nested) {
						value.key = prefix_+value.key;
						option_.push(value);
						$.each(window.getOptionNested(option, value.value, prefix_+'->'), function(index, value_) {
							option_.push(value_);
						});
					}
				});
				return option_;
			}
		}	
		$('.selecttable_dependent').each(function(index, el) {
			var elem = $(this);
			if (!elem.data('selecttable_dependent_load') && !elem.hasClass('selecttable_autocomplete')) {
				elem.data('selecttable_dependent_load', 1);
				var value      = elem.data('value');
				var token      = elem.data('token');
				var dependent  = elem.data('dependent');
				var nested     = elem.data('nested');
				var option_def = elem.html();
				var multiple   = (elem.attr('multiple') != undefined) ? 1 : 0;
				$('[name="'+dependent+'"]').on('change', function(event) {
					var v = $(this).val(); 
					if (v) {
						$.ajax({
							url: _URL+'_T/getdata',
							type: 'POST',
							dataType: 'json',
							data: {token: token, v: v},
						})
						.done(function(out) {
							if (nested) {
								out = window.getOptionNested(out);
							}
							var option = option_def;
							$.each(out, function(index, val) {
								if (multiple) {
									if ($.inArray(val.value, value) != -1) {
										val.selected = ' selected';
									}else{
										val.selected = '';
									}
								}else{
									if (value == val.value) {
										val.selected = ' selected';
									}else{
										val.selected = '';
									}
								}
								option += '<option value="'+val.value+'"'+val.selected+'>'+val.key+'</option>';
							});
							elem.html(option).trigger('change');
						})
						.fail(function() {
							alert("Something Wrong. Please try again later or reload this page !");
						});
					}else{
						elem.html(option_def).trigger('change');
					}
				}).trigger('change');
			}
		});
	}, false);
})();
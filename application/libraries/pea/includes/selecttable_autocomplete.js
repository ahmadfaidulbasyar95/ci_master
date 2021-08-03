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
		$('.selecttable_autocomplete').each(function(index, el) {
			var elem = $(this);
			if (!elem.data('selecttable_autocomplete_load')) {
				elem.data('selecttable_autocomplete_load', 1).hide();
				var value      = elem.data('value');
				var token      = elem.data('token');
				var token2     = elem.data('token2');
				var dependent  = elem.data('dependent');
				var nested     = elem.data('nested');
				var multiple   = (elem.attr('multiple') != undefined) ? 1 : 0;
				var required   = (elem.attr('required') != undefined) ? 'required="required"' : '';
				
				$('<input type="text" class="form-control selecttable_autocomplete_input" value="" '+required+' title="'+elem.attr('title')+'" placeholder="'+elem.attr('title')+'"><div class="selecttable_autocomplete_result list-group" style="display: none;position: absolute;box-shadow: rgba(0, 0, 0, 0.53) 0px 5px 5px;"></div>').insertAfter(elem);
				
				var input  = elem.next('.selecttable_autocomplete_input');
				var result = input.next('.selecttable_autocomplete_result');

				if (dependent != undefined) {
					dependent = $('[name="'+dependent+'"]');
					dependent.on('change', function(event) {
						input.val('').trigger('keyup');
					});
				}

				input.on('keyup', delay(function(event) {
					var s = input.val(); 
					if (s || value) {
						var v = '0'; 
						if (dependent != undefined) {
							v = dependent.val(); 
						}
						$.ajax({
							url: _URL+'_T/getdata',
							type: 'POST',
							dataType: 'json',
							data: {token: (s) ? token : token2, v: v, s: (s) ? s : value},
						})
						.done(function(out) {
							if (nested) {
								out = window.getOptionNested(out);
							}
							var option = [];
							$.each(out, function(index, val) {
								if (multiple) {
									if ($.inArray(val.value, value) != -1) {
										val.selected = 1;
									}else{
										val.selected = 0;
									}
								}else{
									if (value == val.value) {
										val.selected = 1;
									}else{
										val.selected = 0;
									}
								}
								option.push(val);
							});
							option_selected = '';
							option_html     = '';
							$.each(option, function(index, val) {
								if (val.selected) {
									option_selected += '<option value="'+val.value+'" selected>'+val.key+'</option>';
									option_html     += '<a href="#" class="list-group-item active" data-value="'+val.value+'">'+val.key+'</a>';
								}else{
									option_html += '<a href="#" class="list-group-item" data-value="'+val.value+'">'+val.key+'</a>';
								}
							});
							elem.html(option_selected).trigger('change');
							result.html(option_html);
							if (s) {
								result.show();
							}else{
								input.trigger('focusout');
							}
						})
						.fail(function() {
							alert("Something Wrong. Please try again later or reload this page !");
						});
					}else{
						elem.html('').trigger('change');
						result.html('');
					}
				}, 1000)).trigger('keyup');
			}
		});
		$('body').on('click', '.selecttable_autocomplete_result a', function(event) {
			event.preventDefault();
			var item     = $(this);
			var result   = item.parent('.selecttable_autocomplete_result'); 
			var input    = result.prev('.selecttable_autocomplete_input');
			var elem     = input.prev('.selecttable_autocomplete');
			var multiple = (elem.attr('multiple') != undefined) ? 1 : 0;
			if (multiple) {
				// multiselect
			}else{
				result.children('.active').removeClass('active');
				item.addClass('active');
				elem.html('<option value="'+item.data('value')+'" selected>'+item.html()+'</option>').trigger('change');
			}
		});
		$('body').on('focusin', '.selecttable_autocomplete_input', function(event) {
			$(this).next('.selecttable_autocomplete_result').show();
		});
		$('body').on('focusout', '.selecttable_autocomplete_input', function(event) {
			var input = $(this);
			setTimeout(function() {
				input.next('.selecttable_autocomplete_result').hide();
				var elem     = input.prev('.selecttable_autocomplete');
				var multiple = (elem.attr('multiple') != undefined) ? 1 : 0;
				if (multiple) {
					// multiselect
				}else{
					input.val(elem.children('option').html());
				}
			}, 400);
		});
	}, false);
})();
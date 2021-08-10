(function() {
	window.addEventListener('load', function() { 
		var form          = $('#s_form');
		var params        = $('#s_params');
		var params_result = $('#s_params_result');
		var params_add    = $('#s_params_add');
		var value         = params.val();
		var i_text        = params_result.find('.i_text')[0].outerHTML;
		var i_number      = params_result.find('.i_number')[0].outerHTML;
		var i_select      = params_result.find('.i_select')[0].outerHTML;
		params_result.find('.s_args').html('');
		var input = params_result.html();
		params_result.html('');
		params.hide();
		var index       = 0;
		var methods_def = {
			'setCaption' : {'text' : 'Caption'},
			'setType'    : {'text' : 'Attribute Type'},
			'addTip'     : {'text' : 'Tip'},
			'addClass'   : {'text' : 'Class'},
			'addAttr'    : {'text' : 'Attributes'},
			'setFormat'  : {
				'select' : {
					'-- Select Format --' : '',
					'Email'               : 'email',
					'URL'                 : 'url',
					'Phone'               : 'phone',
					'Number'              : 'number'
				}
			},
			'setDefaultValue' : {'text' : 'Default Value'},
		};
		var methods_ref = {
			'date' : {
				'setDateFormat'      : {'text' : 'd M Y'},
				'setDateFormatInput' : {'text' : 'DD MMM YYYY'},
				'setMinDate'         : {'text' : ''},
				'setMaxDate'         : {'text' : ''},
			},
			'datetime' : {
				'setDateFormat'      : {'text' : 'd M Y H:i:s'},
				'setDateFormatInput' : {'text' : 'DD MMM YYYY HH:mm:ss'},
				'setMinDate'         : {'text' : ''},
				'setMaxDate'         : {'text' : ''},
			},
			'file' : {
				'setFolder'           : {'text' : 'Folder Path'},
				'setAllowedExtension' : {'text' : 'Default: jpg,jpeg,gif,png,bmp'},
				'setResize'           : {'number' : 'Max Size'},
				'setThumbnail'        : {
					'number' : 'Max Size',
					'text'   : 'Prefix'
				},
				'setImageClick'     : {},
				'setDocumentViewer' : {},
				'setNameEncode' 		: {},
				'setUrlExpire'			: {'number' : 'Minutes'},
			},
			'select' : {
				'addOption' : {'text' : 'Option'}
			},
			'multiselect' : {
				'addOption' : {'text' : 'Option'}
			},
			'textarea' : {
				'setHtmlEditor' : {}
			}
		};
		var methods_def_html = '<option value="">-- Select Method --</option>';
		$.each(methods_def, function(index, val) {
			methods_def_html += '<option value="'+index+'">'+index+'</option>';
		});
		value = (value) ? JSON.parse(value) : [];
		if (value != undefined) {
			setTimeout(function() {
				$.each(value, function(index, val) {
					params_add.data('value', val['method']).data('args', val['args']).trigger('click');
				});
				params_add.data('value', '').data('args', []);
			}, 80);
		}else{
			value = [];
		}
		setTimeout(function() {
			params_add.trigger('click');		
		}, 90);

		function methods_update(el) {
			var o = methods_def_html;
			var v = form.val();
			if (methods_ref[v] != undefined) {
				$.each(methods_ref[v], function(index, val) {
					o += '<option value="'+index+'">'+index+'</option>';
				});
			}
			el.html(o);
			el.each(function(index, el_) {
				var el_  = $(this);
				var el_v = el_.data('value');
				if (el_v != undefined) {
					el_.val(el_v);
				}
			});
			el.trigger('change');
		}

		params_add.on('click', function(event) {
			event.preventDefault();
			params_result.append(input.replace(/{index}/g, index));
			index++;
			var m = $('.s_method').last();
			var e = $(this);
			var v = e.data('value');
			if (v) {
				m.data('value', v).data('args', e.data('args'));
			}
			methods_update(m);
		});
		params_result.on('click', 'a', function(event) {
			event.preventDefault();
			var el = $(this).parents('.form-inline');
			el.slideUp(200);
			setTimeout(function() {
				el.remove();
			}, 200);
		});

		form.on('change', function(event) {
			methods_update($('.s_method'));
		});

		params_result.on('change', '.s_method', function(event) {
			var el    = $(this);
			var v     = el.val();
			var args  = el.parents('.form-inline').find('.s_args');
			var args_ = '';
			el.data('value', v);
			if (v) {
				var form_v    = form.val();
				var args_list = (methods_def[v] != undefined) ? methods_def[v] : methods_ref[form_v][v];
				if (args_list != undefined) {
					var index    = args.data('index');
					var args_val = el.data('args');
					var args_idx = 0;
					if (args_val == undefined) {
						args_val = [];
					}
					$.each(args_list, function(idx_, val_) {
						var val_curr = (args_val[args_idx] != undefined) ? args_val[args_idx] : '';
						switch (idx_) {
							case 'text':
								args_ += i_text.replace(/{index}/g, index).replace(/{data}/g, val_).replace(/{value}/g, val_curr);
								break;
							case 'number':
								args_ += i_number.replace(/{index}/g, index).replace(/{data}/g, val_).replace(/{value}/g, val_curr);
								break;
							case 'select':
								var val_o = '';
								$.each(val_, function(idx__, val__) {
									if (val__ == val_curr) {
										val__ += '" selected="selected';
									}
									val_o += '<option value="'+val__+'">'+idx__+'</option>';
								});
								args_ += i_select.replace(/{index}/g, index).replace(/{data}/g, val_o);
								break;
						}
						args_idx++;
					});
				}
			}
			args.html(args_);
		});
	}, false);
})();
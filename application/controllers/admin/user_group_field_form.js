(function() {
	window.addEventListener('load', function() { 
		var form          = $('#s_form');
		var params        = $('#s_params');
		var params_result = $('#s_params_result');
		var params_add    = $('#s_params_add');
		var input         = params_result.html();
		var index         = 0;
		var methods_def   = {
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

		function methods_update(el) {
			var o = methods_def_html;
			var v = form.val();
			if (methods_ref[v] != undefined) {
				$.each(methods_ref[v], function(index, val) {
					o += '<option value="'+index+'">'+index+'</option>';
				});
			}
			el.html(o).trigger('change');
		}

		params_result.html('');

		params_add.on('click', function(event) {
			event.preventDefault();
			params_result.append(input.replace(/{index}/, index));
			index++;
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
	}, false);
})();
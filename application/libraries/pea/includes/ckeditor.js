(function() {
	window.addEventListener('load', function() { 
		$.ajax({
			url: _URL+'_T/ckeditor',
			type: 'GET',
			dataType: 'json',
			data: {},
		})
		.done(function(out) {
			CKEDITOR.config.contentsCss = out.css;
			$('.textarea_html_editor').each(function(index, el) {
				CKEDITOR.replace($(this).attr('id'));
			});
		});		
	}, false);
})();
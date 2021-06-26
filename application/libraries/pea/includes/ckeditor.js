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
			var h = CKEDITOR.config.height;
			$('.textarea_html_editor').each(function(index, el) {
				var z = $(this).attr('rows');
				CKEDITOR.config.height = (z == undefined) ? h : z*20;
				CKEDITOR.replace($(this).attr('id'));
			});
		});		
	}, false);
})();
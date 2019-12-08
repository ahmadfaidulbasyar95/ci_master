(function() {
	window.addEventListener('load', function() { 
		$('.textarea_html_editor').each(function(index, el) {
			CKEDITOR.replace($(this).attr('id'));
		});
	}, false);
})();
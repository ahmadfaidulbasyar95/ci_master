(function() {
	window.addEventListener('load', function() { 
		var x = $('#modal-modal_processing', window.parent.document);
		if (x.length) {
			$('.form_pea_edit').children('.panel').css('margin-bottom', '0');
			setTimeout(function() {
				x.find('iframe').height($('html').height());
			}, 100);
		}
	}, false);
})();
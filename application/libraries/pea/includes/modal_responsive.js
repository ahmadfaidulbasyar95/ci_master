(function() {
	window.addEventListener('load', function() { 
		var x = $('#modal-modal_processing', window.parent.document);
		if (x.length) {
			setTimeout(function() {
				x.find('iframe').height($('html').height());
			}, 100);
		}
	}, false);
})();
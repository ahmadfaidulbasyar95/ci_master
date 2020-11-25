(function() {
	window.addEventListener('load', function() { 
		$('.orderby_button').parents('tbody').addClass('orderby_wrapper');
		$('.orderby_wrapper').each(function(index, el) {
			$(this).sortable({
				placeholder: "ui-state-highlight"
			});
		});
	}, false);
})();
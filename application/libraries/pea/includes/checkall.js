(function() {
	window.addEventListener('load', function() { 
		$('body').on('change', '.checkbox.checkall input', function(event) {
			var elem = $(this);
			var idx  = elem.parents('tr').children('th').index(elem.parents('th'));
			elem.parents('thead').next('tbody').children('tr').each(function(index, el) {
				$(this).children('td').eq(idx).find('input').prop('checked', elem.prop('checked'));
			});
		});
	}, false);
})();
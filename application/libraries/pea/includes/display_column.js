(function() {
	window.addEventListener('load', function() { 
		setTimeout(function() {
			$('.pea_roll_table').each(function(index, el) {
				var el = $(this);
				var d  = el.find('.pea_roll_display');
				if (el.height() - 30 < d.children('.dropdown-menu').height()) {
					d.removeClass('dropup').addClass('dropdown');
					var w = el.parent('.table-responsive');
					if (w.length) {
						d.on('show.bs.dropdown', function(event) {
							w.removeClass('table-responsive');
						});
						d.on('hide.bs.dropdown', function(event) {
							w.addClass('table-responsive');
						});
					}
				} 
			});
		}, 1000);
		$('input[type="checkbox"]','.pea_roll_display').on('change', function(event) {
			$(this).parents('.pea_roll_display').children('.dropdown-toggle').trigger('click');
		});
	}, false);
})();
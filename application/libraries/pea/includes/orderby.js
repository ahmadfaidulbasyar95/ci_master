(function() {
	window.addEventListener('load', function() { 
		$('.orderby_button').parents('tbody').addClass('orderby_wrapper');
		$('.orderby_wrapper').each(function(index, el) {
			var e = $(this);
			var l = parseInt(e.parents('.form_pea_roll').data('pagination_offset'))+1;
			e.find('.orderby_input').each(function(index, el) {
				$(this).val(l+index);
			});
			e.sortable({
				placeholder: "ui-state-highlight",
				update: function(event, ui) {
					e.find('.orderby_input').each(function(index, el) {
						$(this).val(l+index);
					});
				}
			});
		});
	}, false);
})();
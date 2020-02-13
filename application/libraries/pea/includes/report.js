(function() {
	window.addEventListener('load', function() {
		$('.form_pea_roll_report').on('click', 'button', function(event) {
			var frm = $(this).parents('.form_pea_roll');
			frm.attr('target', '_BLANK');
			setTimeout(function() {
				frm.removeAttr('target');
			}, 1000);
		});
	}, false);
})();
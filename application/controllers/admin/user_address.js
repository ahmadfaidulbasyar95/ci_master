(function() {
	window.addEventListener('load', function() { 
		var email = $('#i_email').parents('.form-group');
		var phone = $('#i_phone').parents('.form-group');
		var main  = $('#i_main');
		
		main.on('change', function(event) {
			if (main.prop('checked')) {
				email.slideUp(200);
				phone.slideUp(200);
			}else{
				email.slideDown(200);
				phone.slideDown(200);
			}
		});
		setTimeout(function() {
			main.trigger('change');
		}, 500);
	}, false);
})();
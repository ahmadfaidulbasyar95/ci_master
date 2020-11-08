(function() {
	window.addEventListener('load', function() { 
		var z_index_last = 1;
		$('#_start').on('click', function(event) {
			event.preventDefault();
			$('#_menu').toggle(300).toggleClass('active');
		});
		$('.menu_close').on('click', function(event) {
			$('#_menu.active').toggle(300).toggleClass('active');
		});
		$('body').on('click', '._iframe_maximize', function(event) {
			event.preventDefault();
			$(this).children('.fa').toggleClass('fa-window-maximize').toggleClass('fa-window-restore');
			$(this).parents('._iframes').toggleClass('_iframe_move');
		});
		$('body').on('click', '._iframes', function(event) {
			$('._iframe_active').removeClass('_iframe_active');
			$('._iframe_toggle_active').removeClass('_iframe_toggle_active');
			$(this).css('z-index', z_index_last).addClass('_iframe_active');
			$('._iframe_toggles[data-id="'+$(this).data('id')+'"]').addClass('_iframe_toggle_active');
			z_index_last++;
		});
		$('body').on('click', '._iframe_toggles', function(event) {
			event.preventDefault();
			var el = $('._iframes[data-id="'+$(this).data('id')+'"]');
			if (el.hasClass('_iframe_hide')) {
				el.trigger('click').toggle(300).toggleClass('_iframe_hide');
			}else if (el.hasClass('_iframe_active')) {
				el.toggle(300).toggleClass('_iframe_hide');
			}else{
				el.trigger('click');
			}
		});
		$('body').on('click', '._iframe_minimize', function(event) {
			event.preventDefault();
			var el = $(this).parents('._iframes');
			el.toggle(300).toggleClass('_iframe_hide');
		});
	}, false);
})();
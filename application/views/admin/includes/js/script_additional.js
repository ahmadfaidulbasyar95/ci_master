(function() {
	window.addEventListener('load', function() { 
		function _iframe_resize(el, width = 0, height = 0) {
			var fr = el.find('._iframe_body');
			if (width == 0 || height == 0) {
				fr.css('height', '');
				el.css({
					width : '',
					top : 0,
					left : 0
				}).resizable({
					disabled: true
				}).draggable({
					drag: function( event, ui ) {
						ui.position.left = 0;
						ui.position.top = 0;
					}
				});
			}else{
				fr.css('height', height - 32);
				el.css('width', width).resizable({
					disabled: false,
					resize: function( event, ui ) {
						var width_offside = w_width - ui.position.left - 5;
						if (ui.size.width > width_offside) {
							ui.size.width = width_offside;
						}
						var height_offside = w_height - ui.position.top - 35;
						if (ui.size.height > height_offside) {
							ui.size.height = height_offside;
						}
						fr.css('height', ui.size.height - 32);
						el.data('width', ui.size.width);
						el.data('height', ui.size.height);
					}
				}).draggable({
					drag: function( event, ui ) {
						if(ui.position.left < 0) {
							ui.position.left = 0;
						}
						if(ui.position.top < 0) {
							ui.position.top = 0;
						}
						var left_offside = w_width - el.data('width') - 5;
						if(ui.position.left > left_offside) {
							ui.position.left = left_offside;
						}
						var top_offside = w_height - el.data('height') - 35;
						if(ui.position.top > top_offside) {
							ui.position.top = top_offside;
						}
					}
				});
			}
		}
		var w_width      = $(window).width();
		var w_height     = $(window).height();
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
			var el = $(this).parents('._iframes');
			el.toggleClass('_iframe_move');
			if (el.data('width') == undefined) {
				el.data('width', w_width/2);
			}
			if (el.data('height') == undefined) {
				el.data('height', w_height/2);
			}
			if (el.hasClass('_iframe_move')) {
				_iframe_resize(el, el.data('width'), el.data('height'));
			}else{
				_iframe_resize(el, 0, 0);
			}
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
		$('body').on('click', '._iframe_close', function(event) {
			event.preventDefault();
			var el = $(this).parents('._iframes');
			el.fadeOut(300);
			$('._iframe_toggles[data-id="'+el.data('id')+'"]').remove();
			setTimeout(function() {
				el.remove();
			}, 300);
		});
	}, false);
})();
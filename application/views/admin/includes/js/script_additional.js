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
		var w_width        = $(window).width();
		var w_height       = $(window).height();
		var z_index_last   = 1;
		var maximize_count = 0;
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
				if (w_width < w_height) {
					el.data('width', w_width-5);
				}else{
					el.data('width', w_width/2-2);
					maximize_count++;
					if (maximize_count%2 == 0) {
						el.css('left', w_width/2-3);
					}
				}
			}
			if (el.data('height') == undefined) {
				if (w_width < w_height) {
					el.data('height', w_height/2-18);
					maximize_count++;
					if (maximize_count%2 == 0) {
						el.css('top', w_height/2-18);
					}
				}else{
					el.data('height', w_height-36);
				}
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
		$('body').on('click', '._iframe_refresh', function(event) {
			event.preventDefault();
			var el = $(this).parents('._iframes').find('._iframe_body');
			el.attr('src', el.contents().get(0).location.href);
		});
		$('#_iframe_toggles').sortable({
			placeholder: "ui-state-highlight"
		});
		$('#_iframe_toggles').disableSelection();
		var _iframe_id           = 0;
		var _iframes             = $('#_iframes');
		var _iframe_toggles      = $('#_iframe_toggles');
		var _iframes_item        = _iframes.html();
		var _iframe_toggles_item = _iframe_toggles.html();
		_iframes.html('');
		_iframe_toggles.html('');
		$('body').on('click', 'a[target="_iframe"]', function(event) {
			event.preventDefault();
			_iframe_id++;
			var n = $(this).html();
			var h = $(this).attr('href');
			var i = _iframes_item.toString().replace('{title}', n).replace('{id}', _iframe_id).replace('{url}', h);
			var t = _iframe_toggles_item.toString().replace('{title}', n).replace('{id}', _iframe_id);
			_iframes.prepend(i);
			_iframe_toggles.prepend(t);
			$('._iframes[data-id="'+_iframe_id+'"]').trigger('click').toggle(300);
			$('._iframe_toggles[data-id="'+_iframe_id+'"]').toggle(300);
		});
		$('body').on('click', 'a[target="_iframe_sub"]', function(event) {
			event.preventDefault();
			var el  = $(this);
			var sub = el.next('div');
			if (sub.length) {
				if (el.hasClass('active')) {
					sub.slideUp(300).removeClass('active');
					el.removeClass('active');
				}else{
					sub.slideDown(300).addClass('active');
					el.addClass('active');
				}
			}
		});
		$('a[target="_iframe_sub"]').each(function(index, el) {
			var el  = $(this);
			var sub = el.next('div');
			if (sub.length) {
				if (el.hasClass('active')) {
					sub.addClass('active');
				}else{
					sub.hide();
				}
			}
		});
	}, false);
})();
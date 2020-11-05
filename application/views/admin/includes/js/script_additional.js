(function() {
	window.addEventListener('load', function() { 
		$('#_start').on('click', function(event) {
			event.preventDefault();
			$('#_menu').toggle(300).toggleClass('active');
		});
		$('.menu_close').on('click', function(event) {
			$('#_menu.active').toggle(300).toggleClass('active');
		});
	}, false);
})();
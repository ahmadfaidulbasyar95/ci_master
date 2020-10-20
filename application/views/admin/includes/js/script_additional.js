(function() {
	window.addEventListener('load', function() { 
		$('#_nav_collapse').on('click', function(event) {
			event.preventDefault();
			$('body').toggleClass('_nav_hide');
		});
	}, false);
})();
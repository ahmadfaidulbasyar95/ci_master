(function() {
	window.addEventListener('load', function() { 
		var params        = $('#s_params');
		var params_result = $('#s_params_result');
		var params_add    = $('#s_params_add');
		var input         = params_result.html();
		var index         = 0;

		params_result.html('');

		params_add.on('click', function(event) {
			event.preventDefault();
			params_result.append(input.replace(/{index}/, index));
			index++;
		});
		params_result.on('click', 'a', function(event) {
			event.preventDefault();
			var el = $(this).parents('.form-inline');
			el.slideUp(200);
			setTimeout(function() {
				el.remove();
			}, 200);
		});
	}, false);
})();
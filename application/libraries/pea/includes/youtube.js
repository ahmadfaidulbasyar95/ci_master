(function() {
	window.addEventListener('load', function() { 
		$('form').on('focusout', '[rel="input_youtube"]', function(event) {
			var a = $(this).val();
			if (!a.match(/^[^a-z0-9_\-]+$/i)) {
				if (a.match(/youtube\.com/i) && a.match(/v=/i)) {
					var b = new RegExp("v=([^&/]+)");
				} else
				if (a.match(/youtu\.be/i)) {
					var b = new RegExp("be\/([^&/]+)");
				} else {
					var b = new RegExp("embed\/([^&/]+)");
				}
				var c = b.exec(a);
				if (c != null) {
					a = c[1];
				}
				a = a.split('"').join('').split(' ')[0];
				$(this).val(a);
				if ($(this).next('iframe').length) {
					$(this).next('iframe').attr('src', 'https://www.youtube.com/embed/'+a);
				}else{
					$('<iframe width="500" height="315" src="https://www.youtube.com/embed/'+a+'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>').insertAfter(this);
				}
			}else{
				if ($(this).next('iframe').length) {
					$(this).next('iframe').remove();
				}
			}
		});
		$('[rel="input_youtube"]','form').each(function(index, el) {
			if ($(this).val()) {
				$(this).trigger('focusout');
			}
		});
	}, false);
})();
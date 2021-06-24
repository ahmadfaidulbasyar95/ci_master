(function() {
	window.addEventListener('load', function() { 
		var s_mailtype = $('#s_mailtype');
		var s_message  = $('[rel="s_message"]');
		setTimeout(function() {
			var s_message_html  = s_message.next('div');
			s_mailtype.on('change', function(event) {
				if (s_mailtype.val() == 1) {
					s_message.show().css('visibility', 'inherit');
					s_message_html.hide();
				}else{
					s_message.hide();
					s_message_html.show();
				}
			}).trigger('change');
			s_message.on('focusout', function(event) {
				CKEDITOR.instances[s_message.attr('id')].setData(s_message.val());
			});
		}, 2000);
	}, false);
})();
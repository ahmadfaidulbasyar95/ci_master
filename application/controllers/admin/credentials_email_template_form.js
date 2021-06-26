(function() {
	window.addEventListener('load', function() { 
		var s_mailtype = $('#s_mailtype');
		var s_message  = $('[rel="s_message"]');
		s_message.parent('.form-group').css('opacity', 0);
		setTimeout(function() {
			s_message.parent('.form-group').css('opacity', 1);
			$(s_message[0].outerHTML.replace('name="','name="__').replace('id="','id="__')).insertBefore(s_message);
			s_message          = $('[rel="s_message"]').first();
			var s_message_html = s_message.next('textarea').next('div');
			s_mailtype.on('change', function(event) {
				if (s_mailtype.val() == 1) {
					s_message.show().css('visibility', 'inherit');
					s_message_html.hide();
				}else{
					s_message.hide();
					s_message_html.show();
				}
			}).trigger('change');
		}, 4000);
	}, false);
})();
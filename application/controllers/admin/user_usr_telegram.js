(function() {
	window.addEventListener('load', function() { 
		var telegram_id    = $('#telegram_id');
		var telegram_data  = $('#telegram_data');
		var telegram_input = $('#telegram_input');
		
		telegram_data.on('keyup', function(event) {
			var telegram_data_v = telegram_data.val();
			if (telegram_data_v) {
				telegram_data_v = JSON.parse(telegram_data_v);
				if (telegram_data_v) {
					telegram_input.val('@'+telegram_data_v.username+' '+telegram_data_v.first_name+' '+telegram_data_v.last_name);
				}
			}
		}).trigger('keyup');

		var code = telegram_input.data('code');
		var url  = telegram_input.data('url');
		if (code && url) {
			function telegram_validate() {
				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'json',
					data: {},
				})
				.done(function(out) {
					if (out.ok == true) {
						$.each(out.result, function(index, val) {
							if (val.message.text == '/start '+code) {
								telegram_data.val(JSON.stringify(val.message.from)).trigger('keyup');
								telegram_id.val(val.message.from.id);
							}
						});
					}
					setTimeout(function() {
						telegram_validate();
					}, 5000);
				});
			}
			telegram_validate();
		}
	}, false);
})();
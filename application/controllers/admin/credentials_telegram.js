(function() {
	window.addEventListener('load', function() { 
		var info   = $('#info');
		var active = $('#active');
		var token  = $('#token');
		var data   = $('#data');
		token.on('focusout', function(event) {
			if (active.prop('checked')) {
				var v = $(this).val();
				info.html('<div class="text-center"><i class="fa fa-spin fa-spinner fa-4x"></i><br><h2>Checking Bot Information</h2></div>');
				$.ajax({
					url: 'https://api.telegram.org/bot'+v+'/getMe',
					type: 'GET',
					dataType: 'json',
					data: {},
				})
				.done(function(out) {
					if (out.ok == true) {
						data.val(JSON.stringify(out.result));
						var h = '';
						$.each(out.result, function(index, val) {
							h += '<tr><td>'+index+'</td><td>'+val+'</td></tr>'
						});
						info.html('<h2>Bot Information</h2><table class="table table-bordered table-hover"><tbody>'+h+'</tbody></table>');
						$.ajax({
							url: 'https://api.telegram.org/bot'+v+'/getUpdates',
							type: 'GET',
							dataType: 'json',
							data: {},
						})
						.done(function(out) {
							if (out.ok == true) {
								info.append('<h2>Get Updated Meesages</h2><pre>'+JSON.stringify(out.result, undefined, 2)+'</pre>');
							}
						});
					}else{
						data.val('');
						info.html('');
						alert("Bot Token Error");
					}
				})
				.fail(function() {
					data.val('');
					info.html('');
					alert("Bot Token Error");
				});
			}else{
				data.val('');
				info.html('');
			}
		}).trigger('focusout');
		active.on('change', function(event) {
			token.trigger('focusout');
		});
	}, false);
})();
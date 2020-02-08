(function() {
	window.addEventListener('load', function() { 
		$('.fm_daterangepicker').each(function(index, el) {
			var config = $(this).data('config');
			if (config) {
				var el_id = $(this).attr('id');
				$('#'+el_id).daterangepicker(config , function(start, end, label) {
					$('#'+el_id).prev('input').prev('input').val(start.format('YYYY-MM-DD'));
					$('#'+el_id).prev('input').val(end.format('YYYY-MM-DD'));
				});
				if ($('#'+el_id).prev('input').prev('input').val()) {
					var date = $('#'+el_id).prev('input').prev('input').val();
					if (parseInt(date)) {
						date = new Date(date);
						$('#'+el_id).data('daterangepicker').setStartDate(moment().set({
							'year':       date.getFullYear(),
							'month':      date.getMonth(),
							'date':       date.getDate(),
							'hour':       date.getHours(),
							'minute':     date.getMinutes(),
							'second':     date.getSeconds(),
							'milisecond': date.getMilliseconds()
						}).format(config.locale.format));
					}
				}
				if ($('#'+el_id).prev('input').val()) {
					var date = $('#'+el_id).prev('input').val();
					if (parseInt(date)) {
						date = new Date(date);
						$('#'+el_id).data('daterangepicker').setEndDate(moment().set({
							'year':       date.getFullYear(),
							'month':      date.getMonth(),
							'date':       date.getDate(),
							'hour':       date.getHours(),
							'minute':     date.getMinutes(),
							'second':     date.getSeconds(),
							'milisecond': date.getMilliseconds()
						}).format(config.locale.format));
					}
				}
			}
		});
	}, false);
})();
(function() {
	window.addEventListener('load', function() { 
		$('.fm_datepicker').each(function(index, el) {
			var el  = $(this);
			var cfg = el.data('config');
			var id  = el.attr('id');
			if (cfg && !id) {
				id = 'fm_datepicker_'+Math.random();
				id = id.replace('.','');
				el.attr('id', id);
				id     = $('#'+id);
				var fm = id.prev('input');
				id.daterangepicker(cfg , function(start, end, label) {
					fm.val(start.format('YYYY-MM-DD HH:mm:ss'));
				});
				fm.on('change', function(event) {
					var date = $(this).val();
					if (parseInt(date)) {
						date = new Date(date);
						date = moment().set({
							'year':       date.getFullYear(),
							'month':      date.getMonth(),
							'date':       date.getDate(),
							'hour':       date.getHours(),
							'minute':     date.getMinutes(),
							'second':     date.getSeconds(),
							'milisecond': date.getMilliseconds()
						}).format(cfg.locale.format);
						id.data('daterangepicker').setStartDate(date);
						id.data('daterangepicker').setEndDate(date);
					}
				}).trigger('change');
			}
		});
		$('.fm_daterangepicker').each(function(index, el) {
			var el  = $(this);
			var cfg = el.data('config');
			var id  = el.attr('id');
			if (cfg && !id) {
				id = 'fm_datepicker_'+Math.random();
				id = id.replace('.','');
				el.attr('id', id);
				id      = $('#'+id);
				var fm  = id.prev('input');
				var fm_ = id.prev('input').prev('input');
				id.daterangepicker(cfg , function(start, end, label) {
					fm.val(start.format('YYYY-MM-DD HH:mm:ss'));
					fm_.val(end.format('YYYY-MM-DD HH:mm:ss'));
				});
				fm.on('change', function(event) {
					var date = $(this).val();
					if (parseInt(date)) {
						date = new Date(date);
						date = moment().set({
							'year':       date.getFullYear(),
							'month':      date.getMonth(),
							'date':       date.getDate(),
							'hour':       date.getHours(),
							'minute':     date.getMinutes(),
							'second':     date.getSeconds(),
							'milisecond': date.getMilliseconds()
						}).format(cfg.locale.format);
						id.data('daterangepicker').setStartDate(date);
					}
				}).trigger('change');
				fm_.on('change', function(event) {
					var date = $(this).val();
					if (parseInt(date)) {
						date = new Date(date);
						date = moment().set({
							'year':       date.getFullYear(),
							'month':      date.getMonth(),
							'date':       date.getDate(),
							'hour':       date.getHours(),
							'minute':     date.getMinutes(),
							'second':     date.getSeconds(),
							'milisecond': date.getMilliseconds()
						}).format(cfg.locale.format);
						id.data('daterangepicker').setEndDate(date);
					}
				}).trigger('change');
			}
		});
	}, false);
})();
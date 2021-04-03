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
				id.daterangepicker(cfg);
				id.on('hide.daterangepicker', function(event, picker) {
					fm.val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
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
					}else{
						id.val('');
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
				id.daterangepicker(cfg);
				id.on('hide.daterangepicker', function(event, picker) {
					fm.val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
					fm_.val(picker.endDate.format('YYYY-MM-DD HH:mm:ss'));
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
					}else{
						id.val('');
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
					}else{
						id.val('');
					}
				}).trigger('change');
			}
		});
		$('.fm_datepicker').on('cancel.daterangepicker', function(event, picker) {
			$('#'+picker.element[0].id).val('').prev('input').val('');
		});
		$('.fm_daterangepicker').on('cancel.daterangepicker', function(event, picker) {
			$('#'+picker.element[0].id).val('').prev('input').val('').prev('input').val('');
		});
	}, false);
})();
(function() {
	window.addEventListener('load', function() { 
		if ($('#modal-modal_processing').length == 0) {
			$('body').append('<div class="modal fade" id="modal-modal_processing"> <div class="modal-dialog"> <div class="modal-content"> <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute;right: -10px;top: -10px;padding: 5px;border-radius: 50%;background: white;width: 30px;height: 30px;opacity: 0.8;">&times;</button> <iframe src="http://MODAL_URL" name="modal_processing" style="width: 100%;height: 80vh;border: 0; display: block;" allowfullscreen="allowfullscreen" webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe> </div> </div> </div>');
			$('body').on('click', '.modal_processing', function(event) {
				event.preventDefault();
				if ($(this).hasClass('modal_large')) {
					$('.modal-dialog','#modal-modal_processing').css({
						'width':     '1200px',
						'max-width': 'calc(100% - 20px)'
					});
				}else{
					$('.modal-dialog','#modal-modal_processing').removeAttr('style');
				}
				if ($(this).hasClass('modal_reload')) {
					$('#modal-modal_processing').addClass('modal_reload');
				}else {
					$('#modal-modal_processing').removeClass('modal_reload');
				}
				$('#modal-modal_processing').modal('show');
				$('iframe','#modal-modal_processing').css('height', '80vh').attr('src', $(this).attr('href'));
			});
			$("#modal-modal_processing").on("hidden.bs.modal", function () {
				if ($(this).hasClass('modal_reload')) {
					window.location.href = window.location.href;
				}
			});
		}
	}, false);
})();
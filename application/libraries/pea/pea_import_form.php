<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<form action="" method="POST" role="form">
	<div class="form-group" id="upload_excell_wrap">
		<label for="">Upload Excell File</label>
		<input type="file" class="form-control" id="upload_excell" placeholder="Upload Excell File">
	</div>
	<div class="form-group" id="upload_excell_submit_wrap" style="display: none;">
		<button type="button" class="btn btn-default" id="upload_excell_back">Back</button>
		<button type="button" class="btn btn-primary" id="upload_excell_verify">Verify Only</button>
		<button type="button" class="btn btn-primary" id="upload_excell_submit">Verify & Submit</button>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Status</th>
						<th>Pending</th>
						<th>Inserted</th>
						<th>Updated</th>
						<th>Failed</th>
					</tr>
				</thead>
				<tbody>
					<tr id="upload_excell_msg"></tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="form-group" id="upload_excell_submit_next_wrap" style="display: none;">
		<button type="button" class="btn btn-default" id="upload_excell_next">Next</button>
	</div>
</form>
<div class="table-responsive">
	<table class="table table-hover">
		<thead>
			<tr>
				<th colspan="<?php echo count($fields); ?>">
					<div id="upload_excell_row_count_wrap" style="display: none;">Total Row : <b id="upload_excell_row_count"></b> <span id="upload_excell_speed"></span></div>
				</th>
				<th class="text-right">
					<select id="upload_excell_filter_status" style="display: none;">
						<option value="all">-- Select Status --</option>
						<option value="0">Pending</option>
						<option value="1">Inserted</option>
						<option value="2">Updated</option>
						<option value="3">Failed</option>
					</select>
					<input type="text" id="upload_excell_filter" placeholder="Filter" style="display: none;">
				</th>
			</tr>
			<tr>
				<th>NO</th>
				<?php foreach ($fields as $key => $value): ?>
					<th><?php echo $value; ?></th>
				<?php endforeach ?>
				<th></th>
			</tr>
		</thead>
		<tbody id="upload_excell_table">
		</tbody>
	</table>
</div>
<style type="text/css">
	#upload_excell_table tr[data-status="1"] ,
	#upload_excell_table tr[data-status="2"] {
		background: #00800030;
	}
	#upload_excell_table tr[data-status="3"] {
		background: #ff000036;
	}
</style>
<script type="text/javascript">
	(function() {
		window.addEventListener('load', function() { 
			$('#upload_excell_submit,#upload_excell_verify').on('click', function(event) {
				event.preventDefault();
				if (window.upload_excell_submit_run==2) {
					alert('Already Submited');
				}else{
					if (confirm(($(this).attr('id') == 'upload_excell_submit') ? 'Submit ?' : 'Verify ?')) {
						window.upload_excell_submit_run    = ($(this).attr('id') == 'upload_excell_submit') ? 2 : 1;
						window.upload_excell_submit_index  = 0;
						window.upload_excell_submit_report = [window.upload_excell_result.length, 0, 0, 0];

						window.upload_excell_submit(($(this).attr('id') == 'upload_excell_submit') ? 'submit' : 'verify');
					}
				}
			});
			$('#upload_excell_next').on('click', function(event) {
				event.preventDefault();
				$('#upload_excell_wrap').slideUp(200);
				$('#upload_excell_submit_next_wrap').slideUp(200);
				$('#upload_excell_submit_wrap').slideDown(200);
			});
			$('#upload_excell_back').on('click', function(event) {
				event.preventDefault();
				$('#upload_excell_wrap').slideDown(200);
				$('#upload_excell_submit_wrap').slideUp(200);
				if (window.upload_excell_result.length) {
					$('#upload_excell_submit_next_wrap').slideDown(200);
				}
			});
			$('#upload_excell').on('change', function(event) {
				if (window.upload_excell_submit_run) {
					if (confirm('Stop Previous Process ?')) {
						UploadExel('#upload_excell','<?php echo implode('|', $fields); ?>',0,'upload_excell_end', 'upload_excell_start', 'upload_excell_error');
					}
				}else{
					UploadExel('#upload_excell','<?php echo implode('|', $fields); ?>',0,'upload_excell_end', 'upload_excell_start', 'upload_excell_error');
				}
			});
			var upload_excell_filter_timeout;
			$('#upload_excell_filter_status').on('change', function(event) {
				$('#upload_excell_filter').trigger('keydown');
			});
			$('#upload_excell_filter').on('keydown', function(event) {
				var text_input  = $(this);
				var data_filter = [];
				clearTimeout(upload_excell_filter_timeout);
				upload_excell_filter_timeout = setTimeout(function() {
					window.upload_excell_result.filter(function(index) {
						if (index.join('|').toLowerCase().search(text_input.val().toLowerCase()) != -1) {
							if ($('#upload_excell_filter_status').val()=='all' || $('#upload_excell_filter_status').val()==index['status']) {
								data_filter.push(index);
							}
						}
					});
					window.upload_excell_result_filter = data_filter;
					window.upload_excell_show('#upload_excell','<?php echo implode('|', $fields); ?>',0, data_filter);
				}, 300);
			});
			setInterval(function() {
				if (window.upload_excell_submit_run) {
					$('#upload_excell_speed').html('( '+window.upload_excell_submit_speed+' data/s )');
					window.upload_excell_submit_speed  = 0;
				}else{
					$('#upload_excell_speed').html('');
				}
			}, 1000);
		}, false);
	})();
	window.upload_excell_result        = [];
	window.upload_excell_result_filter = [];
	window.upload_excell_submit_run    = 0;
	window.upload_excell_submit_index  = 0;
	window.upload_excell_submit_report = [0,0,0,0];
	window.upload_excell_submit_speed  = 0;
	
	window.upload_excell_submit = function(act) {
		if (window.upload_excell_submit_run) {
			var row = window.upload_excell_result[window.upload_excell_submit_index];
			if (row != undefined) {
				$.ajax({
					url: $('#upload_excell_table').parents('form').attr('action'),
					type: 'POST',
					dataType: 'json',
					data: {act: $('#upload_excell_table').parents('form').data('submit_name')+'_'+act <?php foreach ($post_name as $key => $value) {echo ', '.$value.': row['.($key + 1).']';} ?>},
				})
				.done(function(out) {
					if (window.upload_excell_result[window.upload_excell_submit_index] != undefined) {
						window.upload_excell_result[window.upload_excell_submit_index]['message'] = out.msg;
						if (out.ok) {
							window.upload_excell_result[window.upload_excell_submit_index]['status'] = out.result.status;
						}else{
							window.upload_excell_result[window.upload_excell_submit_index]['status'] = 3;
						}
						window.upload_excell_submit_speed ++;
						window.upload_excell_submit_report[window.upload_excell_result[window.upload_excell_submit_index]['status']] = window.upload_excell_submit_report[window.upload_excell_result[window.upload_excell_submit_index]['status']] + 1;
						window.upload_excell_submit_report[0] = window.upload_excell_submit_report[0] - 1;
						$('tr[data-index='+row[0]+']','#upload_excell_table').attr('data-status', window.upload_excell_result[window.upload_excell_submit_index]['status']).children('td').last().html(out.msg);
						$('#upload_excell_msg').html('<td>Running</td><td>'+window.upload_excell_submit_report[0]+'</td><td>'+window.upload_excell_submit_report[1]+'</td><td>'+window.upload_excell_submit_report[2]+'</td><td>'+window.upload_excell_submit_report[3]+'</td>');
						window.upload_excell_submit_index = window.upload_excell_submit_index + 1;
						window.upload_excell_submit(act);
					}
				});
			}else{
				$('#upload_excell_msg').html('<td>Completed</td><td>'+window.upload_excell_submit_report[0]+'</td><td>'+window.upload_excell_submit_report[1]+'</td><td>'+window.upload_excell_submit_report[2]+'</td><td>'+window.upload_excell_submit_report[3]+'</td>');
			}
		}
	}
	window.upload_excell_error = function(s_input, header, sheet, error_msg) {
		$('#upload_excell_table').html('<tr><td colspan="<?php echo count($fields)+1; ?>" class="text-center"><h3>Error : '+error_msg+'</h3></td></tr>');
		$('#upload_excell_wrap').slideDown(200);
		$('#upload_excell_submit_wrap').slideUp(200);
	}
	window.upload_excell_start = function(s_input, header, sheet) {
		$('#upload_excell_table').html('<tr><td colspan="<?php echo count($fields)+1; ?>" class="text-center"><h3>Uploading ...</h3></td></tr>');
		$('#upload_excell_row_count').html('');
		$('#upload_excell_row_count_wrap').slideUp(200);
		$('#upload_excell_filter').val('').slideUp(200);
		$('#upload_excell_filter_status').val('all').slideUp(200);
		$('#upload_excell_wrap').slideUp(200);
		$('#upload_excell_submit_next_wrap').slideUp(200);
		window.upload_excell_result        = [];
		window.upload_excell_result_filter = [];
		window.upload_excell_submit_run    = 0;
	}
	window.upload_excell_end = function(s_input, header, sheet, data_row) {
		$('#upload_excell_msg').html('');
		$('#upload_excell_row_count_wrap').slideDown(200);
		$('#upload_excell_filter').slideDown(200);
		$('#upload_excell_filter_status').slideDown(200);
		$('#upload_excell_submit_wrap').slideDown(200);
		$.each(data_row, function(index, val) {
			data_row[index]['status']  = 0;
			data_row[index]['message'] = '';
		});
		window.upload_excell_result = data_row;
		window.upload_excell_show(s_input, header, sheet, data_row);
	}
	window.upload_excell_show = function(s_input, header, sheet, data_row) {
		var out = '';
		$('#upload_excell_row_count').html((data_row.length == window.upload_excell_result.length) ? data_row.length : data_row.length+' / '+window.upload_excell_result.length );
		$.each(data_row, function(index, val) {
			out += '<tr data-index="'+val[0]+'" data-status="'+val['status']+'"><td>'+val[0]+'</td><?php foreach (array_keys($fields) as $value) {echo '<td>\'+val['.($value + 1).']+\'</td>';} ?><td>'+val['message']+'</td></tr>';
		});
		$('#upload_excell_table').html(out);
	}
</script>

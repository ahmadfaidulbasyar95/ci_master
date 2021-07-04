<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class _notif_model extends CI_Model {

	public $_root    = '';

	function __construct()
	{
		parent::__construct();

		$this->load->model('_db_model');

		$this->_root = FCPATH;
	}

	function load($type = 0)
	{
		?>
		<script type="text/javascript">
			(function() {
				window.addEventListener('load', function() { 
					var _notif       = $('#notif');
					var _notif_badge = $('#notif_badge');
					var _notif_show  = [];
					function _notif_load() {
						var type = '<?php echo $type; ?>';
						$.ajax({
							url: _URL+'_T/notif',
							type: 'POST',
							dataType: 'json',
							data: {
								type: type
							},
						})
						.done(function(out) {
							var attr       = ('<?php echo $type; ?>' == 1) ? ' target="_iframe"' : '';
							var _notif_add = '';
							if (out.unread.length) {
								$.each(out.unread, function(index, val) {
									if ($.inArray(val.id, _notif_show) == -1) {
										var d = new Date(val.created);
										_notif_show.push(val.id);
										_notif_add += '<li class="active"><a href="'+_URL+'_T/notif_detail?id='+val.id+'&type='+type+'"'+attr+'><b>'+val.title+'</b><br>'+val.info+'<br><small><i>'+d.toDateString()+' '+d.toLocaleTimeString()+'</i></small></a></li>';
									}
								});
								_notif_badge.html((out.unread.length > 9) ? '9+' : out.unread.length).show();
							}else{
								_notif_badge.html('').hide();
							}
							$.each(out.read, function(index, val) {
								if ($.inArray(val.id, _notif_show) == -1) {
									var d = new Date(val.created);
									_notif_show.push(val.id);
									_notif_add += '<li><a href="'+_URL+'_T/notif_detail?id='+val.id+'&type='+type+'"'+attr+'><b>'+val.title+'</b><br>'+val.info+'<br><small><i>'+d.toDateString()+' '+d.toLocaleTimeString()+'</i></small></a></li>';
								}
							});
							_notif.prepend(_notif_add);
							setTimeout(function() {
								_notif_load();
							}, 5000);
						});
					}
					if (_notif.length) {
						_notif_load();
						_notif.on('click', 'a', function(event) {
							$(this).parent('li').removeClass('active');
						});
					}
				}, false);
			})();
		</script>
		<?php
	}

	function send($user_id = 0, $group_id = 0, $title = '', $info = '', $url = '')
	{
		if (is_array($user_id)) {
			foreach ($user_id as $id) {
				$this->send($id, $group_id, $title, $info, $url);
			}
		}elseif (is_array($group_id)) {
			foreach ($group_id as $id) {
				$this->send($user_id, $id, $title, $info, $url);
			}
		}elseif ($title and $info and $url) {
			$this->_db_model->insert('user_notif', array(
				'user_id'  => $user_id,
				'group_id' => $group_id,
				'title'    => $title,
				'info'     => $info,
				'url'      => $url,
				'type'     => 0,
			));
		}
	}

	function sendAdmin($user_id = 0, $group_id = 0, $title = '', $info = '', $url = '')
	{
		if (is_array($user_id)) {
			foreach ($user_id as $id) {
				$this->sendAdmin($id, $group_id, $title, $info, $url);
			}
		}elseif (is_array($group_id)) {
			foreach ($group_id as $id) {
				$this->sendAdmin($user_id, $id, $title, $info, $url);
			}
		}elseif ($title and $info and $url) {
			$this->_db_model->insert('user_notif', array(
				'user_id'  => $user_id,
				'group_id' => $group_id,
				'title'    => $title,
				'info'     => $info,
				'url'      => $url,
				'type'     => 1,
			));
		}
	}

	function sendEmail($tpl_name = '', $to = '', $data = array())
	{
		include_once __DIR__.'/../libraries/file.php';

		$path = $this->_root.'files/notif_email/'.date('YmdHis').'-';

		lib_file_write($path.mt_rand(1,999), json_encode(array(
			'tpl_name' => $tpl_name,
			'to'       => $to,
			'data'     => $data,
		)));
	}

	function sendWA($tpl_name = '', $to = '', $data = array())
	{
		include_once __DIR__.'/../libraries/file.php';

		$path = $this->_root.'files/notif_wa/'.date('YmdHis').'-';

		lib_file_write($path.mt_rand(1,999), json_encode(array(
			'tpl_name' => $tpl_name,
			'to'       => $to,
			'data'     => $data,
		)));
	}
}

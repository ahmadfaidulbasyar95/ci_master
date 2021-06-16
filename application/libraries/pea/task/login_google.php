<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->library('session');

if (isset($_GET['code'])) {
	$result = json_decode(file_get_contents('https://oauth2.googleapis.com/tokeninfo?id_token='.urlencode($_GET['code'])), 1);
	if (empty($result['email'])) {
		show_error('Invalid Code', 401, '401 Unauthorized');
	}else{
		$_SESSION['user_login_google'] = $result;
		if (isset($_SESSION['user_login_google_return'])) {
			$return = $_SESSION['user_login_google_return'];
			unset($_SESSION['user_login_google_return']);
		}else{
			$return = $this->_pea_model->_url;
		}
		redirect($return);
	}
}else{
	if (isset($_GET['return'])) {
		$_SESSION['user_login_google_return'] = $_GET['return'];
	}
	?>
	<html itemscope="" itemtype="http://schema.org/Article">
	<head>
		<script async="" defer="" src="https://apis.google.com/js/client:platform.js?onload=start"></script>
		<script>
			function start() {
				var hash   = window.location.hash.substr(1);
				var result = hash.split('&').reduce(function (res, item) {
					var parts     = item.split('=');
					res[parts[0]] = parts[1];
					return res;
				}, {});
				if (result.id_token == undefined) {
					gapi.load('auth2', function() {
						auth2 = gapi.auth2.init({
							client_id: '2692769944-qs2v2o9tga5i66797jh01p8f7gauujno.apps.googleusercontent.com',
							ux_mode: 'redirect',
							redirect_uri: '<?php echo $this->_pea_model->_url; ?>_T/login_google'
						});
						auth2.signIn();
					});
				}else{
					window.location.href = window.location.origin+window.location.pathname+'?code='+result.id_token;
				}
			}
		</script>
	</head>
	<body></body>
	</html>
	<?php
}
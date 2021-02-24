#!/usr/bin/php
<?php
echo "Your Project Name [ENTER]: ";
$handle = fopen ("php://stdin","r");
$PNAME  = trim(fgets($handle));
if ($PNAME) {
	$PATH = __DIR__.'/../'.$PNAME;
	$ok   = 0;
	if (!is_dir($PATH)) {
		install_exec("mkdir \"$PATH\"");
		$ok = 1;
	}else{
		echo "Directory Exists\nContinue (y/n) ? ";
		$handle   = fopen ("php://stdin","r");
		$continue = trim(fgets($handle));
		if ($continue == 'y') {
			$ok = 1;
		}else{
			echo "Project Installation Canceled\n";
		}
	}
	if ($ok) {
		install_exec("cd \"$PATH\"");

		chdir($PATH);
		$BASENAME = basename(__DIR__);

		install_exec("ln -s ../".$BASENAME."/.gitignore ./");
		install_exec("ln -s ../".$BASENAME."/.htaccess ./");
		install_exec("cp ../".$BASENAME."/index.php ./");
		install_exec("ln -s ../".$BASENAME."/system ./");

		install_exec("mkdir application");
		install_exec("mkdir files");
		install_exec("ln -s ../../".$BASENAME."/files/index.html ./files/");
		install_exec("ln -s ../../".$BASENAME."/files/.htaccess ./files/");
		chmod('files', 0777);

		install_exec("mkdir application/cache");
		install_exec("ln -s ../../../".$BASENAME."/application/cache/index.html ./application/cache/");
		chmod('application/cache/', 0777);

		if (!is_dir($PATH.'/application/config')) {
			install_exec("mkdir application/config");
			install_exec("cp ../".$BASENAME."/application/config/* ./application/config/");
		}
		install_exec("ln -s ../../".$BASENAME."/application/config_custom ./application/");
		
		install_exec("mkdir application/controllers");
		install_exec("ln -s ../../../".$BASENAME."/application/controllers/index.html ./application/controllers/");
		install_exec("ln -s ../../../".$BASENAME."/application/controllers/_Pea.php ./application/controllers/");
		install_exec("ln -s ../../../".$BASENAME."/application/controllers/User.php ./application/controllers/");
		install_exec("ln -s ../../../".$BASENAME."/application/controllers/Welcome.php ./application/controllers/");
		install_exec("ln -s ../../../".$BASENAME."/application/controllers/admin ./application/controllers/");
		
		install_exec("mkdir application/core");
		install_exec("ln -s ../../../".$BASENAME."/application/core/index.html ./application/core/");
		
		install_exec("mkdir application/helpers");
		install_exec("ln -s ../../../".$BASENAME."/application/helpers/index.html ./application/helpers/");
		
		install_exec("mkdir application/hooks");
		install_exec("ln -s ../../../".$BASENAME."/application/hooks/index.html ./application/hooks/");
		
		install_exec("mkdir application/language");
		install_exec("ln -s ../../../".$BASENAME."/application/language/index.html ./application/language/");
		install_exec("ln -s ../../../".$BASENAME."/application/language/english ./application/language/");
		
		install_exec("mkdir application/libraries");
		install_exec("ln -s ../../../".$BASENAME."/application/libraries/index.html ./application/libraries/");
		install_exec("ln -s ../../../".$BASENAME."/application/libraries/excel ./application/libraries/");
		install_exec("ln -s ../../../".$BASENAME."/application/libraries/pdf ./application/libraries/");
		install_exec("ln -s ../../../".$BASENAME."/application/libraries/pea ./application/libraries/");
		install_exec("ln -s ../../../".$BASENAME."/application/libraries/output.php ./application/libraries/");
		install_exec("ln -s ../../../".$BASENAME."/application/libraries/pagination.php ./application/libraries/");
		install_exec("ln -s ../../../".$BASENAME."/application/libraries/path.php ./application/libraries/");
		install_exec("ln -s ../../../".$BASENAME."/application/libraries/table.php ./application/libraries/");
		install_exec("ln -s ../../../".$BASENAME."/application/libraries/file.php ./application/libraries/");
		install_exec("ln -s ../../../".$BASENAME."/application/libraries/bsv.php ./application/libraries/");
		install_exec("ln -s ../../../".$BASENAME."/application/libraries/tabs.php ./application/libraries/");
		
		install_exec("mkdir application/logs");
		install_exec("ln -s ../../../".$BASENAME."/application/logs/index.html ./application/logs/");
		
		install_exec("mkdir application/models");
		install_exec("ln -s ../../../".$BASENAME."/application/models/index.html ./application/models/");
		install_exec("ln -s ../../../".$BASENAME."/application/models/_db_model.php ./application/models/");
		install_exec("ln -s ../../../".$BASENAME."/application/models/_encrypt_model.php ./application/models/");
		install_exec("ln -s ../../../".$BASENAME."/application/models/_pea_model.php ./application/models/");
		install_exec("ln -s ../../../".$BASENAME."/application/models/_tpl_model.php ./application/models/");
		
		install_exec("mkdir application/third_party");
		install_exec("ln -s ../../../".$BASENAME."/application/third_party/index.html ./application/third_party/");
		
		install_exec("mkdir application/views");
		install_exec("ln -s ../../../".$BASENAME."/application/views/index.html ./application/views/");
		install_exec("ln -s ../../../".$BASENAME."/application/views/welcome_message.php ./application/views/");
		install_exec("ln -s ../../../".$BASENAME."/application/views/errors ./application/views/");
		install_exec("ln -s ../../../".$BASENAME."/application/views/admin ./application/views/");
		install_exec("ln -s ../../../".$BASENAME."/application/views/User ./application/views/");
		
		install_exec("ln -s ../../".$BASENAME."/application/.htaccess ./application/");
		install_exec("ln -s ../../".$BASENAME."/application/index.html ./application/");
	}
}else{
	echo "Project Installation Canceled\n";
}
echo "\n";

function install_exec($EX='')
{
	echo $EX."\n";
	echo shell_exec($EX);
}

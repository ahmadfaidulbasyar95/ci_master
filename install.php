#!/usr/bin/php
<?php
echo "Your Project Name [ENTER]: ";
$handle = fopen ("php://stdin","r");
$PNAME  = trim(fgets($handle));
if ($PNAME) {
	$PATH = __DIR__.'/../'.$PNAME;
	if (!is_dir($PATH)) {
		install_exec("mkdir \"$PATH\"");
		install_exec("cd \"$PATH\"");
		chdir($PATH);
		$BASENAME = basename(__DIR__);

		install_exec("ln -s ../".$BASENAME."/.gitignore ./");
		install_exec("ln -s ../".$BASENAME."/.htaccess ./");
		install_exec("ln -s ../".$BASENAME."/index.php ./");
		install_exec("ln -s ../".$BASENAME."/system ./");
		install_exec("mkdir application");
		install_exec("mkdir application/cache");
		install_exec("mkdir application/config");
		install_exec("mkdir application/controllers");
		install_exec("mkdir application/core");
		install_exec("mkdir application/helpers");
		install_exec("mkdir application/hooks");
		install_exec("mkdir application/language");
		install_exec("mkdir application/libraries");
		install_exec("mkdir application/logs");
		install_exec("mkdir application/models");
		install_exec("mkdir application/third_party");
		install_exec("mkdir application/views");
		install_exec("ln -s ../../".$BASENAME."/application/.htaccess ./application/");
		install_exec("ln -s ../../".$BASENAME."/application/index.html ./application/");
	}else{
		echo "Directory Exists\n";
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

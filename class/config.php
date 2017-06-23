<?php

	error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
	$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/config.ini.php');
	if ($config === false)
		$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/config.ini');
	if ($config === false)
		$config = [
			"db_host"					=>	"localhost",
			"db_port"					=>	"",
			"db_name"					=>	"twicecast",
			"db_user"					=>	"api",
			"db_password"			=>	"",
			"token_secret"			=>	"secret",
			"token_chat_secret"	=>	"secret",
			"token_file_secret"		=>	"secret"
		];
	$_SESSION["config"] = $config;
?>
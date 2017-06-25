<?php

	error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
	$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/config.ini.php', true);
	if ($config === false)
		$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/config.ini', true);
	if ($config === false)
		$config = [
			"database"		=> [
				"host"			=>	"localhost",
				"port"			=>	"",
				"name"		=>	"twicecast",
				"user"			=>	"api",
				"password"	=>	""
			],
			"application"	=> [
				"token"		=>	"secret"
			],
			"chat"				=> [
				"protocol"	=>	"ws",
				"ssl"				=>	true,
				"host"			=>	"localhost",
				"port"			=>	"",
				"token"		=>	"secret"
			],
			"repository"		=> [
				"protocol"	=>	"ws",
				"ssl"				=>	true,
				"host"			=>	"localhost",
				"port"			=>	"",
				"token"		=>	"secret"
			]
		];
	$_SESSION["config"] = $config;
?>
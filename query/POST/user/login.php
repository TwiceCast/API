<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');

	if (isset($_POST['mail']) and isset($_POST['password']))
	{
		$auth = new Authentication();
		$auth->setMail($_POST['mail']);
		$auth->setPassword($_POST['password']);
		$token = $auth->generateJWT();
		if ($token !== false)
			echo $token;
		else
			echo '{"error":"Something wrong append"}';
	}
	else
		echo '{"error":"Missing parameters"}';
?>
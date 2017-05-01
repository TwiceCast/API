<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');

	$postdata = json_decode(file_get_contents('php://input'));
	if (isset($postdata->email) and isset($postdata->password))
	{
		$auth = new Authentication();
		$auth->setMail($postdata->email);
		$auth->setPassword($postdata->password);
		$token = $auth->generateJWT();
		if ($token !== false)
			echo $token;
		else
			echo '{"error":"Something wrong append"}';
	}
	else
		echo '{"error":"Missing parameters"}';
?>
<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');

	$postdata = json_decode(file_get_contents('php://input'));
	$response = new Response(Response::OK);

	if (isset($_GET['accept']))
		$response->setContentType($_GET['accept']);
	if (isset($postdata->email) and isset($postdata->password))
	{
		$auth = new Authentication();
		$auth->setMail($postdata->email);
		$auth->setPassword($postdata->password);
		$token = $auth->generateJWT();
		if ($token !== false)
			$response->setMessage(["token" => "$token"]);
		else
			$response->setMessage(["error" => "Something wrong append"], Response::UNKNOWN);
	}
	else
		$response->setMessage(["error" => "Missing parameters"], Response::MISSPARAM);
	$response->send();
?>
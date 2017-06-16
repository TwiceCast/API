<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');

	$response = new Response(Response::OK);
	try {
		$postdata = json_decode(file_get_contents('php://input'));

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		if (!isset($postdata->email) and !isset($postdata->password))
			throw new ParametersException("Missing parameters", Response::MISSPARAM);
		$auth = new Authentication();
		$auth->setMail($postdata->email);
		$auth->setPassword($postdata->password);
		$token = $auth->generateJWT();
		if ($token === false)
			throw new UnknownException("Something wrong append", Response::UNKNOWN);
		$response->setMessage(["token" => "$token"]);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>
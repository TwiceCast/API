<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/functions.php');

	$response = new Response(Response::OK);
	try {
		$postdata = getPostData();

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		if (!isset($postdata['name']) or !isset($postdata['password']) 
			or !isset($postdata['email']) or !isset($postdata['language']))
			throw new ParametersException("Missing parameters", Response::MISSPARAM);
		$newUser = new User();
		$newUser->setName($postdata['name']);
		$newUser->setEmail($postdata['email']);
		$newUser->setPassword($postdata['password']);
		$newUser->setLanguage($postdata['language']['code']);
	
		if (!$newUser->create())
			throw new UnknownException("Something wrong append", Response::UNKNOWN);
		$response->setMessage($newUser);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>
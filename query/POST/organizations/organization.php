<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/functions.php');

	$response = new Response(Response::OK);
	try {
		$postdata = getPostData();
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);

		$authentication = new Authentication();
		$authentication->verify();
		$user = $authentication->getUserFromToken();
		
		if (!$user)
			throw new UnkownException("Something wrong happened");
		
		if (!isset($postdata['name']) or !isset($postdata['language']))
			throw new ParametersException("Missing parameters", Response::MISSPARAM);
		
		$organization = new Organization();
		$organization->setName($postdata['name']);
		$organization->setLanguage($postdata['language']['code']);
		if (!isset($postdata['private']))
			$postdata['private'] = false;
		$organization->setPrivate($postdata['private']);
		
		if (!$organization->create($user->id))
			throw new UnkownException("Something wrong happened", Response::UNKNOWN);
		
		$response->setMessage($organization);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>
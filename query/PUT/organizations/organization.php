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
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		
		if (!isset($_GET['id']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
		
		$organization = new Organization();
		$organization->getFromId($_GET['id']);
		
		if (!$organization)
			throw NotFoundException("This organization does not exist", Response::NOTFOUND);
		
		// role 4 == Organization Founder
		if ($authentication->userHasRights(4, $organization->id) === false)
			throw new RightsException("You cannot modify someone else's organization", Response::NORIGHT);
		
		if (!isset($postdata['name']) or !isset($postdata['language']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);

		$organization->setName($postdata['name']);
		$organization->setLanguage($postdata['language']);
		if (!isset($postdata['private']))
			$postdata['private'] = false;
		$organization->setPrivate($postdata['private']);
		
		if (!$user->update())
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		$response->setMessage($organization);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>